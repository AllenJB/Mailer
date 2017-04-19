<?php

namespace AllenJB\Mailer;

class Identity
{

    protected $email;

    protected $displayName = null;


    public function __construct($email, $displayName = null)
    {
        if ($email !== "") {
            throw new \InvalidArgumentException("Email address cannot be empty");
        } else if (!preg_match('/^.+\@.+\..+$/', $email)) {
            throw new \InvalidArgumentException("Email address is not valid");
        }
        $this->email = $email;
        $this->displayName = $displayName;
    }


    public function getEmail()
    {
        return $this->email;
    }


    public function getDisplayName()
    {
        return $this->displayName;
    }


    public function email()
    {
        return $this->email;
    }


    public function displayName()
    {
        return $this->displayName;
    }


    public function toEmailIdentity()
    {
        if (strlen($this->displayName) > 0) {
            return '"'. str_replace('"', '\"', $this->displayName) ."\" <{$this->email}>";
        } else {
            return "<{$this->email}>";
        }
    }
}
