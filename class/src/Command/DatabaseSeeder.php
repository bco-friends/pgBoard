<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command;

use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'db:seed')]
class DatabaseSeeder extends Command
{
  protected function configure()
  {
    $this
      ->setDescription('Populates the database with fake data to support pgBoard application development.')
      ->setDefinition(
          new InputDefinition([
            new InputOption('all', null, InputOption::VALUE_OPTIONAL, 'Seed the entire application with a sample amount of data.'),
            new InputOption('table', 't', InputOption::VALUE_OPTIONAL, 'Seed a specific database table with data.'),
            new InputOption('count', 'c', InputOption::VALUE_OPTIONAL, 'Set the number of records to generate.')
          ])
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    global $DB;

    $faker = Factory::create();
    $count = $input->getOption('count') ?? 1;

    for ($i = 0; $i < $count; $i++) {
      $DB->insert(
        'member',
        [
          'name'         => $faker->userName(),
          'email_signup' => $faker->safeEmail(),
          'pass'         => md5($faker->password()),
          'postalcode'   => $faker->postcode(),
          'secret'       => md5($faker->word()),
          'ip'           => $faker->ipv4(),
        ]
      );
    }

    $output->writeln("Seeded {$count} records.");
    return Command::SUCCESS;
  }
}
