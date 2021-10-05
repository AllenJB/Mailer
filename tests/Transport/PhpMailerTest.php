<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Tests\Transport;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\Transport\PhpMailer;
use PHPUnit\Framework\TestCase;

class PhpMailerTest extends TestCase
{


    protected function getMockPhpMailer(): \PHPMailer\PHPMailer\PHPMailer
    {
        $mockbuilder = $this->getMockBuilder(\PHPMailer\PHPMailer\PHPMailer::class);
        $mockbuilder->onlyMethods(['send']);
        $phpmailer = $mockbuilder->getMock();
        $phpmailer->expects($this->once())->method('send')->willReturn(true);

        $reflection = new \ReflectionClass($phpmailer);
        $prop = $reflection->getProperty('exceptions');
        $prop->setAccessible(true);
        $prop->setValue($phpmailer, true);

        return $phpmailer;
    }


    public function testSmtpTextOnly(): void
    {
        $phpmailer = $this->getMockPhpMailer();

        $email = new Email();
        $email->setSubject("Test Subject SMTP");
        $email->setFrom('from@example.com', "Test From Name SMTP");
        $email->setReplyTo('reply-to@example.com');
        $email->setTextBody("Test Message Body SMTP");
        $email->addRecipientsTo(["to@example.com"]);

        $transport = new PhpMailer();
        $transport->resetMailerAfterSend(false);
        $transport->setMailer($phpmailer);
        $transport->setMethodSmtp("smtp.example.com", "smtpuser", "smtppass", 588);
        $transport->send($email);

        $this->assertEquals("smtp", $phpmailer->Mailer);
        $this->assertEquals("smtp.example.com", $phpmailer->Host);
        $this->assertEquals(588, $phpmailer->Port);
        $this->assertEquals("smtpuser", $phpmailer->Username);
        $this->assertEquals("smtppass", $phpmailer->Password);

        $this->assertEquals($email->subject(), $phpmailer->Subject);
        $this->assertEquals($email->from()->email(), $phpmailer->From);
        $this->assertEquals($email->from()->displayName(), $phpmailer->FromName);
        $this->assertEquals($email->bodyText(), $phpmailer->Body);
        $this->assertEquals([[$email->replyTo()->email(), ""]], array_values($phpmailer->getReplyToAddresses()));
    }


    public function testTextAndHtml(): void
    {
        $phpmailer = $this->getMockPhpMailer();

        $email = new Email();
        $email->setSubject("Test Subject SMTP");
        $email->setFrom('from@example.com', "Test From Name");
        $email->setReplyTo('reply-to@example.com');
        $email->setTextBody("Test Message Body");
        $email->setHtmlBody("Test HTML Body");
        $email->addRecipientsTo(["to@example.com"]);
        $ccRecipients = ["cc1@example.com", "cc2@example.com"];
        $email->addRecipientsCc($ccRecipients);
        $bccRecipients = ["bcc1@example.com", "bcc2@example.com"];
        $email->addRecipientsBcc($bccRecipients);

        $transport = new PhpMailer();
        $transport->resetMailerAfterSend(false);
        $transport->setMailer($phpmailer);
        $transport->send($email);

        $this->assertEquals("mail", $phpmailer->Mailer);

        $this->assertEquals($email->subject(), $phpmailer->Subject);
        $this->assertEquals($email->from()->email(), $phpmailer->From);
        $this->assertEquals($email->from()->displayName(), $phpmailer->FromName);
        $this->assertEquals($email->bodyText(), $phpmailer->AltBody);
        $this->assertEquals($email->bodyHtml(), $phpmailer->Body);
        $this->assertEquals([[$email->replyTo()->email(), ""]], array_values($phpmailer->getReplyToAddresses()));

        $expectedRecipients = [];
        foreach ($ccRecipients as $origRecipient) {
            $expectedRecipients[] = [ $origRecipient, "" ];
        }
        $this->assertEquals($expectedRecipients, array_values($phpmailer->getCcAddresses()));

        $expectedRecipients = [];
        foreach ($bccRecipients as $origRecipient) {
            $expectedRecipients[] = [ $origRecipient, "" ];
        }
        $this->assertEquals($expectedRecipients, array_values($phpmailer->getBccAddresses()));
    }
}
