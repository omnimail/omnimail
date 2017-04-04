<?php

namespace Omnimail\Tests;

use Omnimail\Omnimail;
use PHPUnit_Framework_TestCase;
use PHPUnit\Framework\TestCase;

if (class_exists('\PHPUnit\Framework\TestCase')) {
    class BaseTestClass extends TestCase
    {
    }
} else if (class_exists('\PHPUnit_Framework_TestCase')) {
    class BaseTestClass extends PHPUnit_Framework_TestCase
    {
        public function expectException($exception)
        {
            $this->setExpectedException($exception);
        }
    }
}
