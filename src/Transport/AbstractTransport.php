<?php
declare(strict_types = 1);

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\InternalEmail;

abstract class AbstractTransport
{

    protected $tmpPath;

    protected $tmpFiles = [];

    /**
     * @var string "default" or "smtp" are currently supported
     */
    protected $method = "default";

    protected $methodHost = null;

    protected $methodPort = null;

    protected $methodUser = null;

    protected $methodPass = null;


    public function __construct()
    {
        $this->tmpPath = sys_get_temp_dir();
    }


    public function setMethodSmtp(string $hostname, string $username, string $password, int $port = 587) : void
    {
        $this->method = "smtp";
        $this->methodHost = $hostname;
        $this->methodUser = $username;
        $this->methodPass = $password;
        $this->methodPort = $port;

        $this->reconfigureMethod();
    }


    abstract protected function reconfigureMethod() : void;


    public function setTempPath(string $path) : void
    {
        $this->tmpPath = $path;
    }


    public function send(Email $email) : bool
    {
        if (($email->subject() ?? "") === "") {
            throw new \UnexpectedValueException("Email has no subject");
        }
        if (($email->sender() === null) && ($email->from() === null) && $email->replyTo() === null) {
            throw new \UnexpectedValueException("Email has no from / sender identity");
        }
        if ((($email->bodyText() ?? "") === "") && (($email->bodyHtml() ?? "") === "")) {
            throw new \UnexpectedValueException("Email has no body");
        }
        if ((count($email->to()) + count($email->cc()) + count($email->bcc())) < 1) {
            throw new \UnexpectedValueException("Email has no recipients");
        }
        
        $email = new InternalEmail($email);

        $retVal = $this->sendImplementation($email);

        $this->cleanupTmpFiles();

        return $retVal;
    }


    protected abstract function sendImplementation(InternalEmail $email) : bool;


    protected function createTmpFile($extension) : string
    {
        $tmpName = 'email_attach_'. dechex(time()) .'_'. dechex(random_int(0, 4096)) .'.'. $extension;
        if (file_exists($this->tmpPath . $tmpName)) {
            return $this->createTmpFile($extension);
        }
        return $this->tmpPath . $tmpName;
    }


    protected function cleanupTmpFiles() : void
    {
        foreach ($this->tmpFiles as $tmpFile) {
            unlink($tmpFile);
        }
    }

}
