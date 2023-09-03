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

  public function getDefaultParser()
  {
    return new BoardParse($this->bbc, $this->rep, false);
  }

  public static function getHideMediaEmbeds(): array {
    return [
      'youtube' => [
        '[youtube]https://youtu.be/VbhZZnIRPOI?si=x7H3P2hdz4LmJRef[/youtube]',
        '<a href="https://youtu.be/VbhZZnIRPOI?si=x7H3P2hdz4LmJRef" onclick="window.open(this.href); return false;">YOUTUBE REMOVED CLICK TO VIEW</a>'
      ]
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

  /**
   * @covers BoardParse::run
   * @covers BoardParse::youtube
   * @test
   */
  public function it_parses_youtube_urls()
  {
    $parse = $this->getDefaultParser();

    self::assertSame(
      '<object width="425" height="355"><param name="movie" value="https://youtube.com/v/VbhZZnIRPOI?si=x7H3P2hdz4LmJRef"></param><param name="wmode" value="transparent"></param><embed src="https://youtube.com/v/VbhZZnIRPOI?si=x7H3P2hdz4LmJRef" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>',
      $parse->run('[youtube]https://youtu.be/VbhZZnIRPOI?si=x7H3P2hdz4LmJRef[/youtube]')
    );
  }

  /**
   * @covers BoardParse::run
   * @dataProvider getHideMediaEmbeds
   * @test
   */
  public function it_creates_links_when_media_is_hidden(string $link_text, string $expected_html)
  {
    self::assertSame($expected_html, (new BoardParse($this->bbc, $this->rep, true))->run($link_text));
  }
}
