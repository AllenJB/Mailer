<?php
declare(strict_types = 1);

namespace AllenJB\Mailer;

class Email
{

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

    protected $to = [];

    protected $cc = [];

    protected $bcc = [];

    /**
     * @var ?string
     */
    protected $inReplyTo = null;

    protected $references = [];

    protected $attachments = [];


    public function __construct()
    {
    }


    public function setSubject($subject)
    {
        $this->subject = $subject;
    }


    public function setTextBody($body)
    {
        $this->bodyText = $body;
    }


    public function appendTextBody($body)
    {
        $this->bodyText .= $body;
    }


    public function setHtmlBody($body)
    {
        $this->bodyHtml = $body;
    }


    public function addTo($email, $displayName = null)
    {
        $this->to[] = new Identity($email, $displayName);
    }


    public function addCc($email, $displayName = null)
    {
        $this->cc[] = new Identity($email, $displayName);
    }


    public function addBcc($email, $displayName = null)
    {
        $this->bcc = new Identity($email, $displayName);
    }


    public function addRecipientsTo(array $recipients)
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addTo($value, null);
            } else {
                $this->addTo($key, $value);
            }
        }
    }


    public function addRecipientsCc(array $recipients)
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addCc($value, null);
            } else {
                $this->addCc($key, $value);
            }
        }
    }


    public function addRecipientsBcc(array $recipients)
    {
        foreach ($recipients as $key => $value) {
            if (is_int($key)) {
                $this->addBcc($value, null);
            } else {
                $this->addBcc($key, $value);
            }
        }
    }


    public function addAttachment($displayFilename, $contentType, $filePath, $disposition = 'attachment')
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


    public function addAttachmentData($displayFilename, $contentType, $data, $disposition = 'attachment')
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
    public function setFrom($email, $displayName = null)
    {
        $this->from = new Identity($email, $displayName);
    }


    /**
     * Set the identity that the email was sent by (actual sender)
     *
     * @param string $email
     * @param null|string $displayName
     */
    public function setSender($email, $displayName = null)
    {
        $this->sender = new Identity($email, $displayName);
    }


    public function setReplyTo($email, $displayName = null)
    {
        $this->replyTo = new Identity($email, $displayName);
    }


    public function setReturnPath($email)
    {
        $this->returnPath = new Identity($email);
    }


    public function setInReplyTo($messageId)
    {
        $this->inReplyTo = $messageId;
        $this->references[] = $messageId;
    }


    /**
     * @param string|string[] $ref
     */
    public function addReference($ref)
    {
        if (!is_array($ref)) {
            $ref = [$ref];
        }
        $this->references += $ref;
    }


    public function addHeader($header, $value, $overwrite = true)
    {
        if (!$overwrite) {
            if (!array_key_exists($header, $this->addHeaders)) {
                $this->addHeaders[$header] = [];
            }
            $this->addHeaders[$header][] = $value;
        }

        $this->addHeaders[$header] = [$value];
    }


    public function attachments()
    {
        return $this->attachments;
    }


    public function subject()
    {
        return $this->subject;
    }


    public function bodyText()
    {
        return $this->bodyText;
    }


    public function bodyHtml()
    {
        return $this->bodyHtml;
    }


    public function to()
    {
        return $this->to;
    }


    public function cc()
    {
        return $this->cc;
    }


    public function bcc()
    {
        return $this->bcc;
    }


    public function from()
    {
        return $this->from;
    }


    public function sender()
    {
        return $this->sender;
    }


    public function replyTo()
    {
        return $this->replyTo;
    }


    public function returnPath()
    {
        return $this->returnPath;
    }


    public function additionalHeaders()
    {
        return $this->addHeaders;
    }

}
