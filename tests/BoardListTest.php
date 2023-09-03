<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Tests;

use DB;
use BoardList;
use PHPUnit\Framework\TestCase;

class BoardListTest extends TestCase
{
  protected function setUp(): void
  {
    global $DB;
    $DB = self::createMock(DB::class);
  }

  /**
   * @covers BoardList::thread
   * @test
   */
  public function it_prints_an_error_message_when_there_is_no_data_to_display()
  {
    $list = BoardList::init();

    ob_start();
    $list->thread();
    $actual = ob_get_clean();

    self::assertSame(BoardList::NO_DATA_ERROR, $actual);
  }
}
