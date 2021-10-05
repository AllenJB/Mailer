<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\InternalEmail;

class PhpMailer extends AbstractTransport
{

    protected $mailer;

    protected $resetAfterSend = true;


    public function __construct()
    {
        parent::__construct();

        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $this->mailer->XMailer = " ";
    }


    public function setMailer(\PHPMailer\PHPMailer\PHPMailer $mailer): void
    {
        $this->mailer = $mailer;
    }


    /**
     * Toggle resetting the PHPMailer instance after each send. (Default: reset after send)
     * This should be enabled for production, but can be disabled for debugging / tests

     * @param bool $enabled
     */
    public function resetMailerAfterSend(bool $enabled): void
    {
        $this->resetAfterSend = $enabled;
    }


    protected function reconfigureMethod(): void
    {
        switch ($this->method) {
            case "default";
                break;

            case "smtp":
                $this->mailer->isSMTP();
                $this->mailer->Host = $this->methodHost;
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = 'tls';
                $this->mailer->Port = $this->methodPort;
                $this->mailer->Username = $this->methodUser;
                $this->mailer->Password = $this->methodPass;
                break;

            default:
                throw new \DomainException("Unimplemented method for the selected transport");
        }
    }


    protected function sendImplementation(InternalEmail $email): bool
    {
        $this->reset();

        $this->mailer->Subject = $email->subject();

        $this->mailer->setFrom($email->from()->email(), ($email->from()->displayName() ?? ''));
        $this->mailer->addReplyTo($email->replyTo()->email(), ($email->replyTo()->displayName() ?? ''));

        foreach ($email->to() as $identity) {
            $this->mailer->addAddress($identity->email(), ($identity->displayName() ?? ''));
        }

        foreach ($email->cc() as $identity) {
            $this->mailer->addCC($identity->email(), ($identity->displayName() ?? ''));
        }

        foreach ($email->bcc() as $identity) {
            $this->mailer->addBCC($identity->email(), ($identity->displayName() ?? ''));
        }

        foreach ($email->attachments() as $attachment) {
            switch ($attachment['type']) {
                case 'data':
                    $ext = pathinfo($attachment['filename'], PATHINFO_EXTENSION);
                    $tmpFile = $this->createTmpFile($ext);
                    file_put_contents($tmpFile, $attachment['data']);
                    $this->tmpFiles[] = $tmpFile;
                    $this->mailer->addAttachment($tmpFile, $attachment['filename']);
                    break;

                case 'file':
                    $this->mailer->addAttachment($attachment['path'], $attachment['filename']);
                    break;

                default:
                    throw new \UnexpectedValueException("Unhandled attachment type: " . $attachment['type']);
            }
        }

        if ($email->inReplyTo() !== null) {
            $this->mailer->addCustomHeader('In-Reply-To', $email->inReplyTo());
        }

        if (count($email->references())) {
            $encodedRefs = [];
            foreach ($email->references() as $refdMessageId) {
                $encodedRefs[] = '<'. $refdMessageId .'>';
            }
            $headerString = join(" ", $encodedRefs);
            $this->mailer->addCustomHeader('References', $headerString);
        }

        foreach ($email->additionalHeaders() as $header => $values) {
            foreach ($values as $value) {
                $this->mailer->addCustomHeader($header, $value);
            }
        }

        if (($email->bodyText() ?? '') !== '') {
            if ($email->bodyHtml() !== null) {
                $this->mailer->isHTML(true);
                $this->mailer->Body = $email->bodyHtml();
                $this->mailer->AltBody = $email->bodyText();
            } else {
                $this->mailer->isHTML(false);
                $this->mailer->Body = $email->bodyText();
            }
        } else {
            $this->mailer->isHTML(true);
            $this->mailer->Body = $email->bodyHtml();
        }

        $retVal = $this->mailer->send();

        if ($this->resetAfterSend) {
            $this->reset();
        }

        return $retVal;
    }


    protected function reset(): void
    {
        $this->mailer->clearAttachments();
        $this->mailer->clearCustomHeaders();
        $this->mailer->clearAllRecipients();
        $this->mailer->clearReplyTos();
        $this->mailer->Subject = null;
        $this->mailer->Body = null;
        $this->mailer->AltBody = null;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->ErrorInfo = null;
        $this->mailer->Encoding = '8bit';
        $this->mailer->isHTML(false);
    }

}
