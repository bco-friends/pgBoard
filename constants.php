<?php
if (is_readable(__DIR__ . '/constants.override.php')) {
  require_once __DIR__ . '/constants.override.php';
}

if (!defined('LANG')) {
  define("LANG", $_ENV['LANG'] ?? "en");
}

if (!defined('ADMIN_EMAIL')) {
  define("ADMIN_EMAIL", $_ENV['ADMIN_EMAIL'] ?? "admin@domain.com");
}

if (!defined('DB')) {
  define("DB", $_ENV['DB'] ?? "dbname=board user=board password=board");
}

if (!defined('DIR')) {
  define("DIR", $_ENV['DIR'] ?? "/path/to/board/www/");
}

if (!defined('SPHINX_HOST')) {
  define("SPHINX_HOST", $_ENV['SPHINX_HOST'] ?? "localhost");
}

if (!defined('SPHINX_PORT')) {
  define("SPHINX_PORT", $_ENV['SPHINX_PORT'] ?? 3312);
}

if (!defined('REGISTRATION_OPEN')) {
  define("REGISTRATION_OPEN", $_ENV['REGISTRATION_OPEN'] ?? true);
}

if (!defined('REGISTRATION_PASSWORD')) {
  define("REGISTRATION_PASSWORD", $_ENV['REGISTRATION_PASSWORD'] ?? "membersonly"); // set to false to disable this feature
}

if (!defined('MEMBER_REGEXP')) {
  define("MEMBER_REGEXP", $_ENV['MEMBER_REGEXP'] ?? "|^[a-z0-9_-]{3,15}$|"); // regexp to define valid member name
}

if (!defined('INACTIVITY_LOCK_INTERVAL')) {
  define("INACTIVITY_LOCK_INTERVAL", $_ENV['INACTIVITY_LOCK_INTERVAL'] ?? "1 year"); // the amount of time a member can only read the board
}

if (!defined('INACTIVITY_WARNING_INTERVAL')) {
  define("INACTIVITY_WARNING_INTERVAL", $_ENV['INACTIVITY_WARNING_INTERVAL'] ?? "9 months"); // the amount of time before a warning is displayed for inactivity
}

if (!defined('IGNORE_ENABLED')) {
  define("IGNORE_ENABLED", $_ENV['IGNORE_ENABLED'] ?? true); // if you disable this be sure to DELETE * FROM member_ignore
}

if (!defined('IGNORE_PUBLIC')) {
  define("IGNORE_PUBLIC", $_ENV['IGNORE_PUBLIC'] ?? true); // set to false to make ignoring private
}

if ( !defined('IGNORE_BUFFER')) {
  define("IGNORE_BUFFER", $_ENV['IGNORE_BUFFER'] ?? "1 year"); // how long from first post until ignore can be used (set false to disable)
}

if (!defined('IGNORED_THREADS_PUBLIC')) {
  define("IGNORED_THREADS_PUBLIC", $_ENV['IGNORED_THREADS_PUBLIC'] ?? true); // set to false to make thread ignoring private
}

if (!defined('FAVORITES_PUBLIC')) {
  define("FAVORITES_PUBLIC", $_ENV['FAVORITES_PUBLIC'] ?? true); // set to false to make favorite threads private
}

if (!defined('LIST_DEFAULT_LIMIT')) {
  define("LIST_DEFAULT_LIMIT", $_ENV['LIST_DEFAULT_LIMIT'] ?? 100); // number of threads per page
}

if (!defined('COLLAPSE_DEFAULT')) {
  define("COLLAPSE_DEFAULT", $_ENV['COLLAPSE_DEFAULT'] ?? 25); // default value to collapse at
}

if (!defined('COLLAPSE_OPEN_DEFAULT')) {
  define("COLLAPSE_OPEN_DEFAULT", $_ENV['COLLAPSE_OPEN_DEFAULT'] ?? 5); // default number of posts to leave open after collapse
}

if (!defined('UNCOLLAPSE_COUNT_DEFAULT')) {
  define("UNCOLLAPSE_COUNT_DEFAULT", $_ENV['UNCOLLAPSE_COUNT_DEFAULT'] ?? 15); // number of additional posts to show when showing "more"
}

if (!defined('FUNDRAISER_ID')) {
  define("FUNDRAISER_ID", $_ENV['FUNDRAISER_ID'] ?? -1); // id of fundraiser record in database
}

if (!defined('FUNDRAISER_ITEM_NAME')) {
  define("FUNDRAISER_ITEM_NAME", $_ENV['FUNDRAISER_ITEM_NAME'] ?? "Board Hosting"); // item name for paypal ipn to recognize payment
}

if (!defined('FUNDRAISER_EMAIL')) {
  define("FUNDRAISER_EMAIL", $_ENV['FUNDRAISER_EMAIL'] ?? "adminpaypal@domain.com"); // email address for paypal payments
}

if (!defined('VIEW_DATE_FORMAT')) {
  define("VIEW_DATE_FORMAT", $_ENV['VIEW_DATE_FORMAT'] ?? "F jS, Y @ g:i:s a");
}

if (!defined('LIST_DATE_FORMAT')) {
  define("LIST_DATE_FORMAT", $_ENV['LIST_DATE_FORMAT'] ?? "D\&\\n\b\s\p\;M\&\\n\b\s\p\;d\&\\n\b\s\p;Y&\\n\b\s\p\;h:i\&\\n\b\s\p\;a");
}

if (!defined('FORM_SALT')) {
  define("FORM_SALT", $_ENV['FORM_SALT'] ?? "aksjdsa9*^&*@&(@*22@*1");
}

