<?php

namespace Omnimail\Tests;

use Omnimail\Omnimail;
use PHPUnit_Framework_TestCase;
use PHPUnit\Framework\TestCase;

if (class_exists('\PHPUnit\Framework\TestCase')) {
    class BaseTestClass extends TestCase
    {
        public function expectException($exception)
        {
            if (is_callable('parent::expectException')) {
                parent::expectException($exception);
            } else {
                $this->setExpectedException($exception);
            }
        }
    }
} elseif (class_exists('\PHPUnit_Framework_TestCase')) {
    class BaseTestClass extends PHPUnit_Framework_TestCase
    {
        public function expectException($exception)
        {
            $this->setExpectedException($exception);
        }
    }
}
