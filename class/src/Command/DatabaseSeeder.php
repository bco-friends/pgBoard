<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command;

use DB;
use Data;
use Faker\Factory;
use Faker\Generator;
use PgSql\Result;
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
  private Data $Data;
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
    global $DB, $Security;

    $this->DB = $DB;
    $this->Data = new Data($DB, $Security);
    $this->faker = Factory::create();

    $this->generateMembers($input, $output);
    $this->generateThreads($input, $output);

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
          'pass'         => md5('testing123'),
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

  private function generateThreads(InputInterface $input, OutputInterface $output)
  {
    $count = $input->getOption('count') ?? 1000;
    $failures = 0;

    $indexQuery = <<<SQL
        CREATE OR REPLACE FUNCTION random_between(low integer, high integer)
               RETURNS integer
               LANGUAGE plpgsql
               STRICT
               AS \$function\$
               BEGIN
                RETURN floor(random()* (high-low +1) + low);
               END;
              \$function\$;
    SQL;

    $result = $this->DB->query($indexQuery);

    if (is_bool($result)) {
      $output->writeln("Failed to create random_between function.");
    }

    for ($i = 0; $i < $count; $i++) {
      try {
        $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();
        $memberId = pg_fetch_result(
          $this->DB->query("SELECT random_between(min(id), max(id)) from member"),
          0,
          0
        );
        $memberName = pg_fetch_result($this->DB->query("SELECT name FROM member WHERE id = $1", [$memberId]), 0, 0);

        ob_start();
        $result                 = $this->Data->thread_insert([
          'name' => $memberName,
          'pass' => 'testing123',
          'subject' => $this->faker->text(),
          'body' => $this->faker->paragraphs(rand(1, 10), true)
        ]);
        ob_end_clean();
      } catch (\Throwable $e) {
        $failures++;
        continue;
      }
    }

    $output->writeln(
      sprintf(
        "Successfully generated %d new threads out of %d requested.",
        $count - $failures,
        $count,
      )
    );
  }
}
