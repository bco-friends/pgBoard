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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'db:seed')]
class DatabaseSeeder extends Command
{
  private const TEST_PASSWORD = 'testing123';
  private DB $DB;
  private Data $Data;
  private Generator $faker;
  private ProgressBar $progressBar;

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
    global $DB, $Security, $commandline;

    $commandline = true;

    $this->DB = $DB;
    $this->Data = new Data($DB, $Security);
    $this->faker = Factory::create();

    $helper = $this->getHelper('question');

    try {
      $this->createRandomizationQuery();
    } catch (\Exception $e) {
      $output->writeln($e->getMessage());
    }

    $this->generateMembers($helper, $input, $output);
    $this->generateThreads($helper, $input, $output);
    $this->generateReplies($helper, $input, $output);

    return Command::SUCCESS;
  }

  private function generateMembers(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
  {
    $default = 1000;
    $question = new Question("How many members would you like to generate? (Default: {$default}): ");
    $count = $helper->ask($input, $output, $question);

    if (!is_numeric($count)) {
      $count = $input->getOption('count') ?? $default;
    }

    $failures = 0;

    $progressBar = new ProgressBar($output, $count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      $result = $this->DB->insert(
        'member',
        [
          'name'         => $this->faker->userName(),
          'email_signup' => $this->faker->safeEmail(),
          'pass'         => md5(self::TEST_PASSWORD),
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

      $progressBar->advance();
    }

    $output->writeln(
      sprintf(
        "\nSuccessfully generated %d member records.",
        $count - $failures,
      )
    );

    $progressBar->finish();
  }

  private function createRandomizationQuery(): void
  {
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
      throw new \Exception("Failed to create random_between function.");
    }
  }

  private function getRandomMemberId(): int
  {
    return (int)pg_fetch_result(
      $this->DB->query("SELECT random_between(min(id), max(id)) from member"),
      0,
      0
    );
  }

  private function getRandomThreadId(): int
  {
    return (int)pg_fetch_result(
      $this->DB->query("SELECT random_between(min(id), max(id)) from thread"),
      0,
      0
    );
  }

  private function generateThreads(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
  {
    $default = 1000;
    $question = new Question("How many threads would you like to generate? (Default: {$default}): ");
    $count = $helper->ask($input, $output, $question);

    if (!is_numeric($count)) {
      $count = $input->getOption('count') ?? $default;
    }

    $failures = 0;
    $progressBar = new ProgressBar($output, (int) $count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      try {
        $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();
        $memberId = $this->getRandomMemberId();
        $memberName = pg_fetch_result($this->DB->query("SELECT name FROM member WHERE id = $1", [$memberId]), 0, 0);

        ob_start();
        $result                 = $this->Data->thread_insert([
          'name' => $memberName,
          'pass' => self::TEST_PASSWORD,
          'subject' => $this->faker->text(),
          'body' => $this->faker->paragraphs(rand(1, 10), true)
        ]);
        ob_end_clean();
      } catch (\Throwable $e) {
        $failures++;
        $output->writeln($e->getMessage());
        continue;
      } finally {
        $progressBar->advance();
      }
    }

    $progressBar->finish();

    $output->writeln(
      sprintf(
        "\nSuccessfully generated %d new threads out of %d requested.",
        $count - $failures,
        $count,
      )
    );
  }

  public function generateReplies(QuestionHelper $helper, InputInterface $input, OutputInterface $output): void
  {
    $default = 1000;
    $question = new Question("How many thread replies would you like to generate? (Default: {$default}): ");
    $count    = $helper->ask($input, $output, $question);
    $failures = 0;

    if (!is_numeric($count)) {
      $count = $input->getOption('count') ?? $default;
    }

    $progressBar = new ProgressBar($output, (int) $count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();

      $this->Data->thread_post_insert(
        [
          'thread_id' => $this->getRandomThreadId(),
          'body' => $this->faker->paragraphs(rand(1, 10), true)
        ],
        $this->getRandomMemberId()
      );

      $progressBar->advance();
    }

    $progressBar->finish();
  }
}
