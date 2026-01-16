<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Tests;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\InternalEmail;

class InternalEmailTest extends EmailTest
{
    public function constructClassToTest(): Email
    {
        $originalEmail = new Email();
        return new InternalEmail($originalEmail);
    }

    public function testReplyTo(): void
    {
        $originalEmail = $this->constructClassToTest();
        $email = new InternalEmail($originalEmail);

        $testEmail = "reply-to@example.com";
        $email->setReplyTo($testEmail);
        $this->assertEquals($testEmail, $email->replyTo()?->email());
        $this->assertNull($email->replyTo()?->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "Reply To Name";
        $email->setReplyTo($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->replyTo()?->email());
        $this->assertEquals($testDisplayName, $email->replyTo()?->displayName());

        // Test inherited values
        $this->assertEquals($testEmail, $email->from()?->email());
        $this->assertEquals($testDisplayName, $email->from()?->displayName());

        $this->assertEquals($testEmail, $email->sender()?->email());
        $this->assertEquals($testDisplayName, $email->sender()?->displayName());

        $this->assertEquals($testEmail, $email->returnPath()?->email());
        $this->assertEquals($testDisplayName, $email->returnPath()?->displayName());
    }

    public function testSender(): void
    {
        $email = $this->constructClassToTest();

        $testEmail = "sender@example.com";
        $email->setSender($testEmail);
        $this->assertEquals($testEmail, $email->sender()?->email());
        $this->assertNull($email->sender()?->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "Sender Name";
        $email->setSender($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->sender()?->email());
        $this->assertEquals($testDisplayName, $email->sender()?->displayName());

        // Test inherited values
        $this->assertEquals($testEmail, $email->from()?->email());
        $this->assertEquals($testDisplayName, $email->from()?->displayName());

        $this->assertEquals($testEmail, $email->replyTo()?->email());
        $this->assertEquals($testDisplayName, $email->replyTo()?->displayName());

        $this->assertEquals($testEmail, $email->returnPath()?->email());
        $this->assertEquals($testDisplayName, $email->returnPath()?->displayName());
    }

    public function testFrom(): void
    {
        $email = $this->constructClassToTest();

        $testEmail = "from@example.com";
        $email->setFrom($testEmail);
        $this->assertEquals($testEmail, $email->from()?->email());
        $this->assertNull($email->from()?->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "From Name";
        $email->setFrom($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->from()?->email());
        $this->assertEquals($testDisplayName, $email->from()?->displayName());

        // Test inherited values
        $this->assertEquals($testEmail, $email->replyTo()?->email());
        $this->assertEquals($testDisplayName, $email->replyTo()?->displayName());

        $this->assertEquals($testEmail, $email->sender()?->email());
        $this->assertEquals($testDisplayName, $email->sender()?->displayName());

        $this->assertEquals($testEmail, $email->returnPath()?->email());
        $this->assertEquals($testDisplayName, $email->returnPath()?->displayName());
    }

    public function testFromOrderOfPreference(): void
    {
        $email = $this->constructClassToTest();

        $testSender = "sender@example.com";
        $email->setSender($testSender);
        $testReplyTo = "reply-to@example.com";
        $email->setReplyTo($testReplyTo);
        $this->assertEquals($testSender, $email->from()?->email());

        $testFrom = "from@example.com";
        $email->setFrom($testFrom);
        $this->assertEquals($testFrom, $email->from()?->email());
    }

    public function testSenderOrderOfPreference(): void
    {
        $email = $this->constructClassToTest();

        $testFrom = "from@example.com";
        $email->setFrom($testFrom);
        $testReplyTo = "reply-to@example.com";
        $email->setReplyTo($testReplyTo);
        $this->assertEquals($testFrom, $email->sender()?->email());

        $testSender = "sender@example.com";
        $email->setSender($testSender);
        $this->assertEquals($testSender, $email->sender()?->email());
    }

    public function testReplyToOrderOfPreference(): void
    {
        $email = $this->constructClassToTest();

        $testFrom = "from@example.com";
        $email->setFrom($testFrom);
        $testSender = "sender@example.com";
        $email->setSender($testSender);
        $this->assertEquals($testFrom, $email->replyTo()?->email());

        $testReplyTo = "reply-to@example.com";
        $email->setReplyTo($testReplyTo);
        $this->assertEquals($testReplyTo, $email->replyTo()?->email());
    }

    public function testReturnPathOrderOfPreference(): void
    {
        $email = $this->constructClassToTest();

        $testFrom = "from@example.com";
        $email->setFrom($testFrom);
        $testReplyTo = "reply-to@example.com";
        $email->setReplyTo($testReplyTo);
        $this->assertEquals($testFrom, $email->returnPath()?->email());

        $testSender = "sender@example.com";
        $email->setSender($testSender);
        $this->assertEquals($testSender, $email->returnPath()?->email());

        $testReturnPath = "return-path@example.com";
        $email->setReturnPath($testReturnPath);
        $this->assertEquals($testReturnPath, $email->returnPath()?->email());
    }
}
