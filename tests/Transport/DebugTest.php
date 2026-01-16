<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Tests\Transport;

use AllenJB\Mailer\Email;
use AllenJB\Mailer\Transport\Debug;
use PHPUnit\Framework\TestCase;

class DebugTest extends TestCase
{
    public function testNoEmailsSent(): void
    {
        $transport = new Debug();

        $this->assertNull($transport->getLastEmail());
        $this->assertEquals([], $transport->getEmails());
    }

    public function testOneEmailSent(): void
    {
        $email = new Email();
        $email->setSubject(__METHOD__ . " Test 1");

        $transport = new Debug();
        $transport->send($email);

        $this->assertEquals($email, $transport->getLastEmail());

        $allEmails = $transport->getEmails();
        $this->assertCount(1, $allEmails);
        $this->assertEquals($email, $allEmails[0]);

        $transport->clearSentEmails();
        $this->assertNull($transport->getLastEmail());
        $this->assertEquals([], $transport->getEmails());
    }

    public function testMultipleEmailsSent(): void
    {
        $emailsToSend = [];
        for ($i = 0; $i < 5; $i++) {
            $email = new Email();
            $email->setSubject(__METHOD__ . " Test " . $i);
            $emailsToSend[] = $email;
        }

        $transport = new Debug();
        foreach ($emailsToSend as $emailToSend) {
            $transport->send($emailToSend);
        }

        $this->assertEquals($emailsToSend[4], $transport->getLastEmail());

        $allEmails = $transport->getEmails();
        $count = count($emailsToSend);
        $this->assertCount($count, $allEmails);
        for ($i = 0; $i < $count; $i++) {
            $this->assertEquals($emailsToSend[$i], $allEmails[$i]);
        }

        $transport->clearSentEmails();
        $this->assertNull($transport->getLastEmail());
        $this->assertEquals([], $transport->getEmails());
    }
}
