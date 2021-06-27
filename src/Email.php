<?php
declare(strict_types = 1);

namespace AllenJB\Mailer;

class Email
{

    /**
     * @var array<string>
     */
    protected $allowedDispositions = ['attachment', 'inline'];

    /**
     * @var ?string
     */
    protected $subject = null;

    /**
     * @var ?string
     */
    protected $bodyText = null;

    /**
     * @var ?string
     */
    protected $bodyHtml = null;

    /**
     * @var array<string, array<string>>
     */
    protected $addHeaders = [];

    /**
     * @var ?Identity
     */
    protected $from = null;

    /**
     * @var ?Identity
     */
    protected $sender = null;

    /**
     * @var ?Identity
     */
    protected $replyTo = null;

    /**
     * @var ?Identity
     */
    protected $returnPath = null;

    /**
     * @var array<Identity>
     */
    protected $to = [];

    /**
     * @var array<Identity>
     */
    protected $cc = [];

    /**
     * @var array<Identity>
     */
    protected $bcc = [];

    /**
     * @var ?string
     */
    protected $inReplyTo = null;

    /**
     * @var array<string>
     */
    protected $references = [];

    /**
     * @var array
     */
    protected $attachments = [];


    public function __construct()
    {
    }


    public function setSubject(string $subject) : void
    {
        $this->subject = $subject;
    }


    public function setTextBody(string $body) : void
    {
        $this->bodyText = $body;
    }


    public function appendTextBody(string $body) : void
    {
        $this->bodyText .= $body;
    }


    public function setHtmlBody(string $body) : void
    {
        $this->bodyHtml = $body;
    }


    public function addTo(string $email, string $displayName = null) : void
    {
        $this->to[] = new Identity($email, $displayName);
    }


    public function addCc(string $email, string $displayName = null) : void
    {
        $this->cc[] = new Identity($email, $displayName);
    }


    public function addBcc(string $email, string $displayName = null) : void
    {
        $this->bcc[] = new Identity($email, $displayName);
    }

    /**
     * @param string[] $recipients (If the array has keys, the key is the email address and the value the display name)
     */
    public function addRecipientsTo(array $recipients) : void
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addTo($value, null);
            } else {
                $this->addTo($key, $value);
            }
        }
    }


    /**
     * @param string[] $recipients (If the array has keys, the key is the email address and the value the display name)
     */
    public function addRecipientsCc(array $recipients) : void
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addCc($value, null);
            } else {
                $this->addCc($key, $value);
            }
        }
    }


    /**
     * @param string[] $recipients (If the array has keys, the key is the email address and the value the display name)
     */
    public function addRecipientsBcc(array $recipients) : void
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addBcc($value, null);
            } else {
                $this->addBcc($key, $value);
            }
        }
    }


    public function addAttachment(string $displayFilename, string $contentType, string $filePath, string $disposition = 'attachment') : void
    {
        if (!in_array($disposition, $this->allowedDispositions, true)) {
            throw new \InvalidArgumentException("Disposition must be one of: ". implode(', ', $this->allowedDispositions));
        }

        $this->attachments[] = [
            'type' => 'file',
            'disposition' => $disposition,
            'contentType' => $contentType,
            'filename' => $displayFilename,
            'path' => $filePath,
        ];
    }


    public function addAttachmentData(string $displayFilename, string $contentType, $data, string $disposition = 'attachment') : void
    {
        if (!in_array($disposition, $this->allowedDispositions, true)) {
            throw new \InvalidArgumentException("Disposition must be one of: ". implode(', ', $this->allowedDispositions));
        }

        $this->attachments[] = [
            'type' => 'data',
            'disposition' => $disposition,
            'contentType' => $contentType,
            'filename' => $displayFilename,
            'data' => $data,
        ];
    }


    /**
     * Set the identity that the email appears to be from (is sent on behalf of)
     *
     * @param string $email
     * @param null|string $displayName
     */
    public function setFrom(string $email, string $displayName = null) : void
    {
        $this->from = new Identity($email, $displayName);
    }


    /**
     * Set the identity that the email was sent by (actual sender)
     *
     * @param string $email
     * @param null|string $displayName
     */
    public function setSender(string $email, string $displayName = null) : void
    {
        $this->sender = new Identity($email, $displayName);
    }


    public function setReplyTo(string $email, string $displayName = null) : void
    {
        $this->replyTo = new Identity($email, $displayName);
    }


    public function setReturnPath(string $email) : void
    {
        $this->returnPath = new Identity($email);
    }


    public function setInReplyTo(string $messageId) : void
    {
        $this->inReplyTo = $messageId;
        $this->references[] = $messageId;
    }


    /**
     * @param string|string[] $ref
     */
    public function addReference($ref) : void
    {
        if (!is_array($ref)) {
            $ref = [$ref];
        }
        $this->references += $ref;
    }


    public function addHeader(string $header, string $value, bool $overwrite = true) : void
    {
        if (!$overwrite) {
            if (!array_key_exists($header, $this->addHeaders)) {
                $this->addHeaders[$header] = [];
            }
            $this->addHeaders[$header][] = $value;
        }

        $this->addHeaders[$header] = [$value];
    }


    public function attachments() : array
    {
        return $this->attachments;
    }


    public function subject() : ?string
    {
        return $this->subject;
    }


    public function bodyText() : ?string
    {
        return $this->bodyText;
    }


    public function bodyHtml() : ?string
    {
        return $this->bodyHtml;
    }


    public function to() : array
    {
        return $this->to;
    }


    public function cc() : array
    {
        return $this->cc;
    }


    public function bcc() : array
    {
        return $this->bcc;
    }


    public function from() : ?Identity
    {
        return $this->from;
    }


    public function sender() : ?Identity
    {
        return $this->sender;
    }


    public function replyTo() : ?Identity
    {
        return $this->replyTo;
    }


    public function returnPath() : ?Identity
    {
        return $this->returnPath;
    }


    public function additionalHeaders() : array
    {
        return $this->addHeaders;
    }

}
