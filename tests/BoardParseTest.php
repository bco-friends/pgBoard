<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Tests;

use BoardParse;
use PHPUnit\Framework\TestCase;

class BoardParseTest extends TestCase
{
  private $bbc = [];
  private $rep = [];

  protected function setUp(): void
  {
    $this->bbc = [
      "",
      "[u]",
      "[/u]",
      "[i]",
      "[/i]",
      "[em]",
      "[/em]",
      "[quote]",
      "[/quote]",
      "[b]",
      "[/b]",
      "[strong]",
      "[/strong]",
      "[strike]",
      "[/strike]",
      "[code]",
      "[/code]",
      "[sub]",
      "[/sub]",
      "[sup]",
      "[/sup]",
      "[spoiler]",
      "[/spoiler]",
    ];

    $this->rep = [
      "",
      "<span style=\"text-decoration:underline;\">",
      "</span>",
      "<em>",
      "</em>",
      "<em>",
      "</em>",
      "<blockquote>",
      "</blockquote>",
      "<strong>",
      "</strong>",
      "<strong>",
      "</strong>",
      "<strike>",
      "</strike>",
      "<pre>",
      "</pre><div class=clear></div>",
      "<sub>",
      "</sub>",
      "<sup>",
      "</sup>",
      "<span class=\"spoiler\" onclick=\"$(this).next().show();$(this).remove()\">show spoiler</span><span style=\"display:none\">",
      "</span>",
    ];
  }

  /**
   * @covers BoardParse::__construct
   * @test
   */
  public function it_instantiates_a_class_instance()
  {
    self::assertInstanceOf(BoardParse::class, new BoardParse([], [], false));
  }

  /**
   * @covers BoardParse::run
   * @test
   */
  public function it_preps_urls()
  {
    $parse = new BoardParse($this->bbc, $this->rep, false);

    self::assertSame(
      '<a href="https://google.com" class="link" onclick="window.open(this.href); return false;" title="https://google.com">https://google.com</a> [google.com] &raquo;',
      $parse->run('[url]https://google.com[/url]')
    );
  }
}
