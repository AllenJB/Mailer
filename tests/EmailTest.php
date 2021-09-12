<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Tests;

use AllenJB\Mailer\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{

    public function constructClassToTest(): Email
    {
        return new Email();
    }

    public function testSubject(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->subject());

        $testValue = "Test Subject";
        $email->setSubject($testValue);
        $this->assertEquals($testValue, $email->subject());
    }


    public function testTextBody(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->bodyText());

        $testValue = "Test Text Body\nWith Line\nReturns";
        $email->setTextBody($testValue);
        $this->assertEquals($testValue, $email->bodyText());

        $appendValue = "Additional\nBody\nText";
        $email->appendTextBody($appendValue);
        $this->assertEquals($testValue . $appendValue, $email->bodyText());
    }


    public function testHtmlBody(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->bodyHtml());

        $testValue = "<html lang='en'><body><p>Test \n<br />Body</p></body></html>";
        $email->setHtmlBody($testValue);
        $this->assertEquals($testValue, $email->bodyHtml());
    }


    public function testReturnPath(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->returnPath());

        $testEmail = "return-path@example.com";
        $email->setReturnPath($testEmail);
        $this->assertEquals($testEmail, $email->returnPath()->email());
    }


    public function testReplyTo(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->from());
        $this->assertNull($email->replyTo());
        $this->assertNull($email->sender());
        $this->assertNull($email->returnPath());

        $testEmail = "reply-to@example.com";
        $email->setReplyTo($testEmail);
        $this->assertEquals($testEmail, $email->replyTo()->email());
        $this->assertNull($email->replyTo()->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "Reply To Name";
        $email->setReplyTo($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->replyTo()->email());
        $this->assertEquals($testDisplayName, $email->replyTo()->displayName());

        // Reply-To should not affect From or Sender for non-internal mails
        $this->assertNull($email->from());
        $this->assertNull($email->sender());
        $this->assertNull($email->returnPath());
    }


    public function testSender(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->from());
        $this->assertNull($email->replyTo());
        $this->assertNull($email->sender());
        $this->assertNull($email->returnPath());

        $testEmail = "sender@example.com";
        $email->setSender($testEmail);
        $this->assertEquals($testEmail, $email->sender()->email());
        $this->assertNull($email->sender()->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "Sender Name";
        $email->setSender($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->sender()->email());
        $this->assertEquals($testDisplayName, $email->sender()->displayName());

        // Sender should not affect From or Reply-To for non-internal mails
        $this->assertNull($email->from());
        $this->assertNull($email->replyTo());
        $this->assertNull($email->returnPath());
    }


    public function testFrom(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->from());
        $this->assertNull($email->replyTo());
        $this->assertNull($email->sender());
        $this->assertNull($email->returnPath());

        $testEmail = "from@example.com";
        $email->setFrom($testEmail);
        $this->assertEquals($testEmail, $email->from()->email());
        $this->assertNull($email->from()->displayName());

        $email = $this->constructClassToTest();
        $testDisplayName = "From Name";
        $email->setFrom($testEmail, $testDisplayName);
        $this->assertEquals($testEmail, $email->from()->email());
        $this->assertEquals($testDisplayName, $email->from()->displayName());

        // From should not affect Reply-To or Sender for non-internal mails
        $this->assertNull($email->replyTo());
        $this->assertNull($email->sender());
        $this->assertNull($email->returnPath());
    }


    public function testTo(): void
    {
        $email = $this->constructClassToTest();
        $this->assertEquals([], $email->to());

        $testEmail1 = "to1@example.com";
        $email->addTo($testEmail1);
        $recipients = $email->to();
        $this->assertCount(1, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());

        $testEmail2 = "to2@example.com";
        $testName2 = "Second Recipient";
        $email->addTo($testEmail2, $testName2);
        $recipients = $email->to();
        $this->assertCount(2, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());

        $multiRecipients = [
            "to3@example.com",
            "to4@example.com" => "Fourth Recipient",
        ];
        $email->addRecipientsTo($multiRecipients);
        $recipients = $email->to();
        $this->assertCount(4, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());
        $this->assertEquals($multiRecipients[0], $recipients[2]->email());
        $this->assertNull($recipients[2]->displayName());
        $this->assertEquals("to4@example.com", $recipients[3]->email());
        $this->assertEquals("Fourth Recipient", $recipients[3]->displayName());
    }


    public function testCc(): void
    {
        $email = $this->constructClassToTest();
        $this->assertEquals([], $email->cc());

        $testEmail1 = "cc1@example.com";
        $email->addCc($testEmail1);
        $recipients = $email->cc();
        $this->assertCount(1, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());

        $testEmail2 = "cc2@example.com";
        $testName2 = "Second Recipient";
        $email->addCc($testEmail2, $testName2);
        $recipients = $email->cc();
        $this->assertCount(2, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());

        $multiRecipients = [
            "cc3@example.com",
            "cc4@example.com" => "Fourth Recipient",
        ];
        $email->addRecipientsCc($multiRecipients);
        $recipients = $email->cc();
        $this->assertCount(4, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());
        $this->assertEquals($multiRecipients[0], $recipients[2]->email());
        $this->assertNull($recipients[2]->displayName());
        $this->assertEquals("cc4@example.com", $recipients[3]->email());
        $this->assertEquals("Fourth Recipient", $recipients[3]->displayName());
    }


    public function testBcc(): void
    {
        $email = $this->constructClassToTest();
        $this->assertEquals([], $email->bcc());

        $testEmail1 = "bcc1@example.com";
        $email->addBcc($testEmail1);
        $recipients = $email->bcc();
        $this->assertCount(1, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());

        $testEmail2 = "bcc2@example.com";
        $testName2 = "Second Recipient";
        $email->addBcc($testEmail2, $testName2);
        $recipients = $email->bcc();
        $this->assertCount(2, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());

        $multiRecipients = [
            "bcc3@example.com",
            "bcc4@example.com" => "Fourth Recipient",
        ];
        $email->addRecipientsBcc($multiRecipients);
        $recipients = $email->bcc();
        $this->assertCount(4, $recipients);
        $this->assertEquals($testEmail1, $recipients[0]->email());
        $this->assertNull($recipients[0]->displayName());
        $this->assertEquals($testEmail2, $recipients[1]->email());
        $this->assertEquals($testName2, $recipients[1]->displayName());
        $this->assertEquals($multiRecipients[0], $recipients[2]->email());
        $this->assertNull($recipients[2]->displayName());
        $this->assertEquals("bcc4@example.com", $recipients[3]->email());
        $this->assertEquals("Fourth Recipient", $recipients[3]->displayName());

        // Verify that no BCC recipients leak into other recipient fields
        $this->assertEquals([], $email->cc());
        $this->assertEquals([], $email->to());
    }


    public function testReferences(): void
    {
        $email = $this->constructClassToTest();
        $this->assertNull($email->inReplyTo());
        $this->assertEquals([], $email->references());

        $inReplyToId = "reply-to@test.id";
        $email->setInReplyTo($inReplyToId);
        $this->assertEquals($inReplyToId, $email->inReplyTo());
        $refs = $email->references();
        $this->assertCount(1, $refs);
        $this->assertEquals($inReplyToId, $refs[0]);

        $addRef1 = "additional1@test.id";
        $email->addReference($addRef1);
        $this->assertEquals($inReplyToId, $email->inReplyTo());
        $refs = $email->references();
        $this->assertCount(2, $refs);
        $this->assertEquals($inReplyToId, $refs[0]);
        $this->assertEquals($addRef1, $refs[1]);

        $addRefs = ["additional2@test.id", "additional3@test.id"];
        $email->addReference($addRefs);
        $this->assertEquals($inReplyToId, $email->inReplyTo());
        $refs = $email->references();
        $this->assertCount(4, $refs);
        $this->assertEquals($inReplyToId, $refs[0]);
        $this->assertEquals($addRef1, $refs[1]);
        $this->assertEquals($addRefs[0], $refs[2]);
        $this->assertEquals($addRefs[1], $refs[3]);
    }


    public function testAttachments(): void
    {
        $email = $this->constructClassToTest();
        $this->assertEquals([], $email->attachments());

        $email->addAttachmentData("testAttachDataFileName", "text/plain", "testAttachData");
        $email->addAttachmentData("testAttachDataFileName2", "text/plain", "testAttachData2", "inline");
        $email->addAttachment("testAttachDataFile", "text/plain", "/test/AttachDataFile/Path");
        $email->addAttachment("testAttachDataFile2.gif", "image/gif", "/test/AttachDataFile/Path2.gif", "inline");

        $attachments = $email->attachments();
        $this->assertCount(4, $attachments);

        $this->assertEquals("data", $attachments[0]["type"]);
        $this->assertEquals("testAttachDataFileName", $attachments[0]["filename"]);
        $this->assertEquals("text/plain", $attachments[0]["contentType"]);
        $this->assertEquals("testAttachData", $attachments[0]["data"]);
        $this->assertEquals("attachment", $attachments[0]["disposition"]);

        $this->assertEquals("data", $attachments[1]["type"]);
        $this->assertEquals("testAttachDataFileName2", $attachments[1]["filename"]);
        $this->assertEquals("text/plain", $attachments[1]["contentType"]);
        $this->assertEquals("testAttachData2", $attachments[1]["data"]);
        $this->assertEquals("inline", $attachments[1]["disposition"]);

        $this->assertEquals("file", $attachments[2]["type"]);
        $this->assertEquals("testAttachDataFile", $attachments[2]["filename"]);
        $this->assertEquals("text/plain", $attachments[2]["contentType"]);
        $this->assertEquals("/test/AttachDataFile/Path", $attachments[2]["path"]);
        $this->assertEquals("attachment", $attachments[2]["disposition"]);

        $this->assertEquals("file", $attachments[3]["type"]);
        $this->assertEquals("testAttachDataFile2.gif", $attachments[3]["filename"]);
        $this->assertEquals("image/gif", $attachments[3]["contentType"]);
        $this->assertEquals("/test/AttachDataFile/Path2.gif", $attachments[3]["path"]);
        $this->assertEquals("inline", $attachments[3]["disposition"]);
    }


    public function testAdditionalHeaders(): void
    {
        $email = $this->constructClassToTest();
        $this->assertEquals([], $email->additionalHeaders());

        $email->addHeader("Test-Header", "Test header value");
        $headers = $email->additionalHeaders();
        $this->assertCount(1, $headers);
        $this->assertArrayHasKey("Test-Header", $headers);
        $this->assertCount(1, $headers["Test-Header"]);
        $this->assertEquals("Test header value", $headers["Test-Header"][0]);

        $email->addHeader("Test-Header", "Test header new value");
        $headers = $email->additionalHeaders();
        $this->assertCount(1, $headers);
        $this->assertArrayHasKey("Test-Header", $headers);
        $this->assertCount(1, $headers["Test-Header"]);
        $this->assertEquals("Test header new value", $headers["Test-Header"][0]);

        $email->addHeader("Test-Header", "Test header additional value", false);
        $headers = $email->additionalHeaders();
        $this->assertCount(1, $headers);
        $this->assertArrayHasKey("Test-Header", $headers);
        $this->assertCount(2, $headers["Test-Header"]);
        $this->assertEquals("Test header new value", $headers["Test-Header"][0]);
        $this->assertEquals("Test header additional value", $headers["Test-Header"][1]);

        $email->addHeader("Another-Header", "Another Header Value", false);
        $headers = $email->additionalHeaders();
        $this->assertCount(2, $headers);
        $this->assertArrayHasKey("Test-Header", $headers);
        $this->assertCount(2, $headers["Test-Header"]);
        $this->assertEquals("Test header new value", $headers["Test-Header"][0]);
        $this->assertEquals("Test header additional value", $headers["Test-Header"][1]);
        $this->assertArrayHasKey("Another-Header", $headers);
        $this->assertCount(1, $headers["Another-Header"]);
        $this->assertEquals("Another Header Value", $headers["Another-Header"][0]);
    }

}
