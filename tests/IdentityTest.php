<?php
declare(strict_types=1);

namespace AllenJB\Mailer\Tests;

use AllenJB\Mailer\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testEmptyEmailThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Email address cannot be empty");

        new Identity("");
    }

    public function testInvalidEmailThrows(): void
    {
        $invalidEmails = [
            "missingAt.example.com",
            "missingDots@examplecom",
            "@nouser.example.com",
            "nodomain@",
        ];

        foreach ($invalidEmails as $invalidEmail) {
            $exceptionThrown = false;
            try {
                new Identity($invalidEmail);
            } catch (\InvalidArgumentException $e) {
                $exceptionThrown = true;
                $this->assertEquals(
                    "Email address is not valid",
                    $e->getMessage(),
                    "Unexpected exception message ({$e->getMessage()}) for email {$invalidEmail}"
                );
            }
            $this->assertTrue(
                $exceptionThrown,
                "No exception thrown for email {$invalidEmail}"
            );
        }
    }

    public function testHappyPath(): void
    {
        $testCases = [
            [
                "email" => "user@example.com",
                "identity" => "<user@example.com>",
            ],
            [
                "email" => "null@example.com",
                "name" => null,
                "identity" => "<null@example.com>",
            ],
            [
                "email" => "user.with.dots@example.com",
                "identity" => "<user.with.dots@example.com>",
            ],
            [
                "email" => "user@subdomain.example.com",
                "identity" => "<user@subdomain.example.com>",
            ],
            [
                "email" => "users.name@example.com",
                "name" => "Users Name",
                "identity" => '"Users Name" <users.name@example.com>',
            ],
            [
                "email" => "quotes@example.com",
                "name" => 'User "Quotes" Name',
                "identity" => '"User \"Quotes\" Name" <quotes@example.com>',
            ],
        ];

        foreach ($testCases as $testCase) {
            if (array_key_exists("name", $testCase)) {
                $identity = new Identity($testCase["email"], $testCase["name"]);
            } else {
                $identity = new Identity($testCase["email"]);
            }

            $this->assertEquals($testCase["email"], $identity->email());
            $this->assertEquals($testCase["identity"], $identity->toEmailIdentity());
            if (array_key_exists("name", $testCase)) {
                $this->assertEquals($testCase["name"], $identity->displayName());
            } else {
                $this->assertNull($identity->displayName());
            }
        }
    }
}
