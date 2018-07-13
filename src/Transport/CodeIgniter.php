<?php
declare(strict_types = 1);

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\Identity;
use AllenJB\Mailer\InternalEmail;

class CodeIgniter extends AbstractTransport
{

    protected $ci;


    public function __construct()
    {
        parent::__construct();

        $this->ci =& get_instance();
        $this->ci->load->library('email');
    }


    protected function reconfigureMethod() : void
    {
        switch ($this->method) {
            case "default";
                break;

            default:
                throw new \DomainException("Unimplemented for the selected transport");
        }
    }
    
    
    protected function sendImplementation(InternalEmail $email) : bool
    {
        /**
         * @var \CI_Email $mailer;
         */
        $mailer = $this->ci->email;
        $mailer->clear(true);

        $mailer->subject($email->subject());

        $this->setAuthorIdentity($mailer, $email);
        $this->setRecipients($mailer, $email);

        // Attachments
        foreach ($email->attachments() as $attachment)
        {
            switch ($attachment['type']) {
                case 'data':
                    $ext = pathinfo($attachment['filename'], PATHINFO_EXTENSION);
                    $tmpFile = $this->createTmpFile($ext);
                    file_put_contents($tmpFile, $attachment['data']);
                    $this->tmpFiles[] = $tmpFile;
                    $mailer->attach($attachment['path'], $attachment['disposition']);
                    break;

                case 'file':
                    $mailer->attach($attachment['path'], $attachment['disposition']);
                    break;

                default:
                    throw new \UnexpectedValueException("Unhandled attachment type: ". $attachment['type']);
            }
        }


        foreach ($email->additionalHeaders() as $header => $values)
        {
            if (count($values) > 1) {
                trigger_error("CodeIgniter does not support multiple header values ({$header})", E_USER_WARNING);
            }
            $value = array_shift($values);
            $mailer->_set_header($header, $value);
        }

        if (strlen($email->bodyText() ?? '') > 0) {
            if ($email->bodyHtml() !== null) {
                $mailer->set_mailtype('html');
                $mailer->message($email->bodyHtml());
                $mailer->set_alt_message($email->bodyText());
            } else {
                $mailer->set_mailtype('text');
                $mailer->message($email->bodyText());
            }
        } else {
            $mailer->set_mailtype('html');
            $mailer->message($email->bodyHtml());
        }

        $success = $mailer->send();
        $mailer->clear(true);

        $this->cleanupTmpFiles();
        return $success;
    }


    protected function setAuthorIdentity(\CI_Email $mailer, InternalEmail $email)
    {
        $mailer->from($email->from()->email(), ($email->from()->displayName() ?? ''));
        $mailer->reply_to($email->replyTo()->email(), ($email->replyTo()->displayName() ?? ''));

        $mailer->_set_header('Sender', $email->sender()->toEmailIdentity());
        // MUST be done after from because CI sets this automatically recipients from address
        $mailer->_set_header('Return-Path', $email->returnPath()->toEmailIdentity());
    }


    protected function setRecipients(\CI_Email $mailer, InternalEmail $email)
    {
        /**
         * @var Identity $identity
         */
        $recipients = [];
        foreach ($email->to() as $identity) {
            $recipients[] = $identity->toEmailIdentity();
        }
        $mailer->to(implode(', ', $recipients));

        /**
         * @var Identity $identity
         */
        $recipients = [];
        foreach ($email->cc() as $identity) {
            $recipients[] = $identity->toEmailIdentity();
        }
        $mailer->cc(implode(', ', $recipients));

        /**
         * @var Identity $identity
         */
        $recipients = [];
        foreach ($email->bcc() as $identity) {
            $recipients[] = $identity->toEmailIdentity();
        }
        $mailer->bcc(implode(', ', $recipients));
    }


}
