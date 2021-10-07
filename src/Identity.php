<?php
declare(strict_types=1);

namespace AllenJB\Mailer;

class Identity
{

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $displayName = null;


    public function __construct(string $email, ?string $displayName = null)
    {
        if ($email === "") {
            throw new \InvalidArgumentException("Email address cannot be empty");
        } elseif (! preg_match('/^.+\@.+\..+$/', $email)) {
            throw new \InvalidArgumentException("Email address is not valid");
        }
        $this->email = $email;
        $this->displayName = $displayName;
    }


    public function email(): string
    {
        return $this->email;
    }


    public function displayName(): ?string
    {
        return $this->displayName;
    }


    public function toEmailIdentity(): string
    {
        if (($this->displayName !== null) && ($this->displayName !== "")) {
            return '"' . str_replace('"', '\"', $this->displayName) . "\" <{$this->email}>";
        }
        return "<{$this->email}>";
    }
}
