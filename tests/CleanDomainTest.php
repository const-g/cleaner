<?php
namespace Constg\Cleaner\Tests;

use Constg\Cleaner\Clean;

require __DIR__ . '/../vendor/autoload.php';

class CleanDomainTest extends \PHPUnit_Framework_TestCase
{
    public function test_valid_domain() {
        $this->assertEquals('google.com', Clean::domain('google.com'));
    }

    public function test_valid_subDomain() {
        $this->assertEquals('google.com', Clean::domain('mail.google.com'));
    }

    public function test_valid_complex_tld() {
        $this->assertEquals('google.co.uk', Clean::domain('google.co.uk'));
    }

    public function test_valid_complex_tld_subDomain() {
        $this->assertEquals('google.co.uk', Clean::domain('mail.google.co.uk'));
    }

    public function test_valid_allow_subDomain() {
        $this->assertEquals('mail.google.com', Clean::domain('mail.google.com', 1));
    }

    public function test_valid_allow_one_subDomain() {
        $this->assertEquals('mail.google.com', Clean::domain('preprod.mail.google.com', 1));
    }

    public function test_unvalid() {
        $this->assertNull(Clean::domain('google'));
    }

    public function test_url() {
        $this->assertEquals('google.com', Clean::domain('http://www.google.com'));
    }

    public function test_url_subDomain() {
        $this->assertEquals('www.google.com', Clean::domain('http://www.google.com', 1));
    }

    public function test_from_email() {
        $this->assertEquals('google.com', Clean::domain('info@google.com'));
    }
}
