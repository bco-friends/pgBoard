<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command;

use DB;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'db:seed')]
class DatabaseSeeder extends Command
{
  private DB $DB;
  private Generator $faker;

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

    $this->DB = $DB;
    $this->faker = Factory::create();

    $this->generateMembers($input, $output);

    return Command::SUCCESS;
  }

  private function generateMembers(InputInterface $input, OutputInterface $output)
  {
    $count = $input->getOption('count') ?? 1000;
    $failures = 0;

    for ($i = 0; $i < $count; $i++) {
      $result = $this->DB->insert(
        'member',
        [
          'name'         => $this->faker->userName(),
          'email_signup' => $this->faker->safeEmail(),
          'pass'         => md5($this->faker->password()),
          'postalcode'   => $this->faker->postcode(),
          'secret'       => md5($this->faker->word()),
          'ip'           => $this->faker->ipv4(),
        ]
      );

      /*
       * Because database inserts fail silently, we don't know what specifically caused the error with the
       * insert query. For now, increment the failure and deduct it from the total requested so we can report
       * back the number of records that were added.
       */
      if (is_bool($result)) {
        $failures++;
      }
    }

    $output->writeln(
      sprintf(
        "Successfully generated %d member records.",
        $count - $failures,
      )
    );
  }
}
