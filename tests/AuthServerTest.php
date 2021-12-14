<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

include_once('./autentificare/server.php');

final class AuthServerTest extends TestCase
{
    public function testLogin(): void
    {
        $obj = login(['username' => 'admin', 'pass'=>'acces']);
        $this->assertInstanceOf('xajaxResponse', $obj);
    }
}