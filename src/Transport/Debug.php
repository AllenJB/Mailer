<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\InternalEmail;

class Debug extends AbstractTransport
{

    /**
     * @var array<Email>
     */
    protected $sentEmails = [];


    protected function reconfigureMethod(): void
    {
    }


    protected function sendImplementation(InternalEmail $email): bool
    {
        return true;
    }


    public function send(Email $email): bool
    {
        $this->sentEmails[] = $email;
        return true;
    }


    public function clearSentEmails(): void
    {
        $this->sentEmails = [];
    }


    /**
     * @return array<Email>
     */
    public function getEmails(): array
    {
        return $this->sentEmails;
    }


    public function getLastEmail(): ?Email
    {
        if (count($this->sentEmails) === 0) {
            return null;
        }
        return end($this->sentEmails);
    }

}
