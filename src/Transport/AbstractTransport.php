<?php

namespace AllenJB\Mailer\Transport;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\InternalEmail;

abstract class AbstractTransport
{

    protected $tmpPath;

    protected $tmpFiles = [];


    public function __construct()
    {
        $this->tmpPath = sys_get_temp_dir();
    }


    public function setTempPath($path)
    {
        $this->tmpPath = $path;
    }


    public function send(Email $email)
    {
        if (strlen($email->subject()) < 1) {
            throw new \UnexpectedValueException("Email has no subject");
        }
        if (($email->sender() === null) && ($email->from() === null) && $email->replyTo() === null) {
            throw new \UnexpectedValueException("Email has no from / sender identity");
        }
        if ((strlen($email->bodyText()) < 1) && (strlen($email->bodyHtml()) < 1)) {
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


    protected abstract function sendImplementation(InternalEmail $email);


    protected function createTmpFile($extension)
    {
        $tmpName = 'email_attach_'. dechex(time()) .'_'. dechex(random_int(0, 4096)) .'.'. $extension;
        if (file_exists($this->tmpPath . $tmpName)) {
            return $this->createTmpFile($extension);
        }
        return $this->tmpPath . $tmpName;
    }


    protected function cleanupTmpFiles()
    {
        foreach ($this->tmpFiles as $tmpFile) {
            unlink($tmpFile);
        }
    }

}
