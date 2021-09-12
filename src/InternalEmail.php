<?php
declare(strict_types=1);

namespace AllenJB\Mailer;

class InternalEmail extends Email
{

    public function __construct(Email $email)
    {
        parent::__construct();

        $props = get_object_vars($email);
        foreach ($props as $prop => $value) {
            $this->{$prop} = $value;
        }
    }


    public function from(): Identity
    {
        $retVal = ($this->from ?? $this->sender);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function sender(): Identity
    {
        $retVal = ($this->sender ?? $this->from);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function replyTo(): Identity
    {
        $retVal = ($this->replyTo ?? $this->from);
        if ($retVal === null) {
            $retVal = $this->sender;
        }
        return $retVal;
    }


    public function returnPath(): Identity
    {
        return ($this->returnPath ?? $this->sender());
    }

}
