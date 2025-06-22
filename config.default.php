<?php
$baseDir = dirname(__DIR__);

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/constants.php';

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load($baseDir . '/.env');

// functions allowed no matter what your login state
$_allowed_ = array("threadmain","threadlist","threadview","threadviewpost",
                   "threadfirstpost","threadreply","threadpreviewpost",
                   "threadtogglefavorite","mainlogin","mainchangelog","donatemain",
                   "donateaccept","membercreate","memberauthorize");

// menu display
$_menu_ = array("create account"     => array("link" => "/member/create/",
                                              "title" => "create an account",
                                              "code" => "",
                                              "show" => REGISTRATION_OPEN,
                                              "auth" => false),
                "threads"            => array("title" => "back to the home page",
                                              "link" => "/",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => false),
                "messages%MESSAGES%" => array("link" => "/message/list/",
                                              "title" => "view your messages",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "new thread"         => array("link" => "/thread/create/",
                                              "title" => "create a new thread",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "new message"        => array("link" => "/message/create/",
                                              "title" => "send a message to another member",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "search"             => array("link" => "/search/",
                                              "title"=> "search the board",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "chat%CHATTERS%"     => array("link" => "/chat/",
                                              "title" => "chat in realtime",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "profile"            => array("link" => "/member/view/",
                                              "title" => "view my profile",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => true),
                "donate"             => array("link" => "/donate/",
                                              "title" => "donate!",
                                              "code" => "",
                                              "show" => true,
                                              "auth" => false));

// parser find
$_bbc_ = array("","[u]","[/u]",
               "[i]","[/i]",
               "[em]","[/em]",
               "[quote]","[/quote]",
               "[b]","[/b]",
               "[strong]","[/strong]",
               "[strike]","[/strike]",
               "[code]","[/code]",
               "[sub]","[/sub]",
               "[sup]","[/sup]",
               "[spoiler]","[/spoiler]");

// parser replace
$_rep_ = array("","<span style=\"text-decoration:underline;\">","</span>",
               "<em>","</em>",
               "<em>","</em>",
               "<blockquote>","</blockquote>",
               "<strong>","</strong>",
               "<strong>","</strong>",
               "<strike>","</strike>",
               "<pre>","</pre><div class=clear></div>",
               "<sub>","</sub>",
               "<sup>","</sup>",
               "<span class=\"spoiler\" onclick=\"$(this).next().show();$(this).remove()\">show spoiler</span><span style=\"display:none\">","</span>");

/*
* Do not edit below this line unless you know what you're doing!
**/
define("VERSION","2.9.5");

require_once("core.php"); // framework
ini_set("magic_gpc_quotes",false);

if(module(0) == "main" && !cmd(1))
{
  set_cmd(0,"thread");
  set_cmd(1,"list");
}
if(!isset($commandline))
{
  $cache = array(); // no caching for the moment array("threadview","messageview");
  if(!in_array(module(0).func(),$cache))
  {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }
  else
  session_cache_limiter("private");

  session_start();
}

require_once("error.php");          // error handler
require_once("lang/".LANG.".php");  // language file
require_once("class/Security.php"); // security
require_once("class/Core.php");     // common commands
require_once("class/DB.php");       // database
require_once("class/Query.php");    // query creation
require_once("class/Style.php");    // color themes, dynamic styling
require_once("class/Base.php");     // base layout
require_once("class/List.php");     // display for lists
require_once("class/View.php");     // display for views
require_once("class/Parse.php");    // bbcode parser
require_once("class/Form.php");     // forms
require_once("class/Data.php");     // data management
require_once("class/Search.php");   // search management
require_once("class/Admin.php");    // search management
require_once("class/Plugin.php");    // plugins

$DB = new DB(DB,true);
$Security = new BoardSecurity($DB, $_allowed_);
$Parse = BoardParse::init($_bbc_,$_rep_);
if(!session('id') && cookie('board')) $Security->login_cookie();
$Core = new BoardCore($DB, $Security, $Parse);
$Style = new BoardStyle($Core, $DB, session('id'));

if(!isset($commandline))
{
  ob_start();
  if(!$DB->db)
  {
    $Base = Base::init();
    $Base->title("Dead database!");
    $Base->header();
    $Base->footer();
  }
  else
  {
    $Core->command_parse();
    if(get('ajax'))
    {
      $buffer = ob_get_contents();
      ob_end_clean();
      print $buffer;
      exit_clean();
    }
    $buffer = ob_get_contents();
    ob_end_clean();
  }
}
