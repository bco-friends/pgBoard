<?php
declare(strict_types=1);

namespace PgBoard\PgBoard;

use PgBoard\PgBoard\Command\DatabaseSeeder;
use Symfony\Component\Console\Application;

class CommandService
{
  private array $commands = [
    DatabaseSeeder::class,
  ];

  public function __construct(
    private readonly Application $application
  ) {}

  public static function load(): Application {
    $application = new Application();
    $service = new self($application);
    $service->load_commands();

    return $application;
  }

  public function load_commands()
  {
    foreach ($this->commands as $command_class) {
      $this->application->add(new $command_class());
    }

    $this->application->run();
  }
}
