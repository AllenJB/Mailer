<?php
declare(strict_types = 1);

namespace AllenJB\Mailer;

class Identity
{

    protected $email;

    protected $displayName = null;


    public function __construct(string $email, string $displayName = null)
    {
        if ($email !== "") {
            throw new \InvalidArgumentException("Email address cannot be empty");
        } else if (!preg_match('/^.+\@.+\..+$/', $email)) {
            throw new \InvalidArgumentException("Email address is not valid");
        }
        $this->email = $email;
        $this->displayName = $displayName;
    }


    public function getEmail() : string
    {
        return $this->email;
    }


    public function getDisplayName()
    {
        return $this->displayName;
    }


    public function email() : string
    {
        return $this->email;
    }


    public function displayName()
    {
        return $this->displayName;
    }


    public function toEmailIdentity()
    {
        if (($this->displayName ?? "") !== "") {
            return '"'. str_replace('"', '\"', $this->displayName) ."\" <{$this->email}>";
        } else {
            return "<{$this->email}>";
        }
    }
}
