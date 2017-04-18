<?php
declare(strict_types = 1);

namespace AllenJB\Mailer;

class InternalEmail extends Email
{

    public function __construct(Email $email)
    {
        parent::__construct();

        $props = get_class_vars($email);
        foreach ($props as $prop => $value) {
            $this->{$prop} = $value;
        }
    }


    public function from()
    {
        $retVal = ($this->from ?? $this->sender);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function sender()
    {
        $retVal = ($this->sender ?? $this->from);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function replyTo()
    {
        $retVal = ($this->replyTo ?? $this->from);
        if ($retVal === null) {
            $retVal = $this->sender;
        }
        return $retVal;
    }


    public function returnPath()
    {
        return ($this->returnPath ?? $this->sender());
    }

}
