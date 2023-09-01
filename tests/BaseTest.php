<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Tests;

use Base;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
  /**
   * @covers Base::init
   * @test
   */
  public function it_creates_a_class_instance()
  {
    self::assertInstanceOf(Base::class, Base::init());
  }
}
