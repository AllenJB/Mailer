<?php
declare(strict_types = 1);

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\Identity;
use AllenJB\Mailer\InternalEmail;

class PhpMailer extends AbstractTransport
{
    
    protected $mailer;


    public function __construct()
    {
        parent::__construct();
        
        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
    }
    
    
    protected function sendImplementation(InternalEmail $email) : bool
    {
        $this->reset();

        $this->mailer->Subject = $email->subject();

        $this->mailer->setFrom($email->from()->email(), ($email->from()->displayName() ?? ''));
        $this->mailer->addReplyTo($email->replyTo()->email(), ($email->replyTo()->displayName() ?? ''));

        /**
         * @var Identity $identity;
         */
        foreach ($email->to() as $identity) {
            $this->mailer->addAddress($identity->email(), ($identity->displayName() ?? ''));     // Add a recipient
        }


        /**
         * @var Identity $identity;
         */
        foreach ($email->cc() as $identity) {
            $this->mailer->addCC($identity->email(), ($identity->displayName() ?? ''));     // Add a recipient
        }

        /**
         * @var Identity $identity;
         */
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
                    $this->mailer->addAttachment($tmpFile, $attachment['filenae']);
                    break;

                case 'file':
                    $this->mailer->addAttachment($attachment['path'], $attachment['filename']);
                    break;

                default:
                    throw new \UnexpectedValueException("Unhandled attachment type: ". $attachment['type']);
            }
        }

        foreach ($email->additionalHeaders() as $header => $values)
        {
            foreach ($values as $value) {
                $this->mailer->addCustomHeader($header, $value);
            }
        }

        if (strlen($email->bodyText() ?? '') > 0) {
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

        $this->reset();

        return $retVal;
    }
    
    
    protected function reset()
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
