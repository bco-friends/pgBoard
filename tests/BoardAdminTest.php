<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Tests;

use BoardAdmin;
use PHPUnit\Framework\TestCase;

class BoardAdminTest extends TestCase
{
  /**
   * @covers BoardAdmin::__construct
   * @test
   */
  public function it_is_a_class_instance()
  {
    self::assertInstanceOf(BoardAdmin::class, new BoardAdmin());
  }
}
