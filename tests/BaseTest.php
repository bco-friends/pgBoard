<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Tests;

use DB;
use Base;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
  public function setUp(): void
  {
    global $DB;

    $DB = $this->createMock(DB::class);
  }

  public static function getTypes(): array
  {
    return [
      [ Base::LIST_THREAD, 'thread' ],
      [ Base::VIEW_THREAD, 'thread' ],
      [ Base::LIST_THREAD_HISTORY, 'thread' ],
      [ Base::LIST_THREAD_SEARCH, 'thread'],
      [ Base::VIEW_MESSAGE, 'message'],
      [ Base::LIST_MESSAGE_HISTORY, 'message'],
      [ Base::LIST_MESSAGE_SEARCH, 'message'],
      [ Base::LIST_MESSAGE_HISTORY, 'message'],
      [ Base::VIEW_MESSAGE_SEARCH, 'message'],
      [ PHP_INT_MAX, null]
    ];
  }

  /**
   * @covers Base::init
   * @test
   */
  public function it_creates_a_class_instance()
  {
    self::assertInstanceOf(Base::class, Base::init());
  }

  /**
   * @dataProvider getTypes
   * @covers Base::type
   * @test
   */
  public function it_sets_the_table_property(int $type, ?string $expected_table)
  {
    $base = Base::init();
    $base->type($type);

    self::assertSame($expected_table, $base->table);
  }
}
