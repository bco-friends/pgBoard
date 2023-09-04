<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'db:seed')]
class DatabaseSeeder extends Command
{
  protected function configure()
  {
    $this->setDescription('Hello World');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->write('Seeding database data...', true);

    return Command::SUCCESS;
  }
}
