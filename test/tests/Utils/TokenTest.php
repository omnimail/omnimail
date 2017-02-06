<?php

namespace Omnimail\Tests\Utils;

use PHPUnit\Framework\TestCase;
use Omnimail\Utils\Token;

class TokenTest extends TestCase
{
    public function testTokenGeneration()
    {
        $string = Token::generate(1);
        $this->assertEquals(1, strlen($string));
        $string = Token::generate(10);
        $this->assertEquals(10, strlen($string));
        $string = Token::generate(1000);
        $this->assertEquals(1000, strlen($string));
    }
}
