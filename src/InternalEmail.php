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
        $retVal = (($this->from === null) ? $this->sender : $this->from);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function sender()
    {
        $retVal = (($this->sender === null) ? $this->from : $this->sender);
        if ($retVal === null) {
            $retVal = $this->replyTo;
        }
        return $retVal;
    }


    public function replyTo()
    {
        $retVal = (($this->replyTo === null) ? $this->from : $this->replyTo);
        if ($retVal === null) {
            $retVal = $this->sender;
        }
        return $retVal;
    }


    public function returnPath()
    {
        if ($this->returnPath === null) {
            return $this->sender();
        }
        return $this->returnPath;
    }

}
