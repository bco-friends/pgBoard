<?php

declare(strict_types=1);

namespace PgBoard\PgBoard\Command;

use DB;
use Data;
use Faker\Factory;
use Faker\Generator;
use PgBoard\PgBoard\Command\DatabaseSeeder\DataGenerator;
use PgBoard\PgBoard\Command\DatabaseSeeder\MemberGenerator;
use PgBoard\PgBoard\Command\DatabaseSeeder\MessageGenerator;
use PgBoard\PgBoard\Command\DatabaseSeeder\Query;
use PgBoard\PgBoard\Command\DatabaseSeeder\ThreadGenerator;
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
  public const TEST_PASSWORD = 'testing123';
  public const NON_INTERACTIVE = 'no-interaction';

  private DB $db;
  private Data $data;
  private Query $query;
  private Generator $faker;
  private ProgressBar $progressBar;

  /**
   * @var DataGenerator[]
   */
  private array $generators = [
    MemberGenerator::class,
    ThreadGenerator::class,
    MessageGenerator::class,
  ];

  protected function configure()
  {
    $this
      ->setDescription('Populates the database with fake data to support pgBoard application development.')
      ->setDefinition(
        new InputDefinition([
          new InputOption(
            'all',
            null,
            InputOption::VALUE_OPTIONAL,
            'Seed the entire application with a sample amount of data.'
          ),
          new InputOption('table', 't', InputOption::VALUE_OPTIONAL, 'Seed a specific database table with data.'),
          new InputOption('count', 'c', InputOption::VALUE_OPTIONAL, 'Set the number of records to generate.'),
        ])
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    global $DB, $Security, $commandline;

    $commandline = true;

    $this->db    = $DB;
    $this->data  = new Data($DB, $Security);
    $this->query = new Query($DB);
    $this->faker = Factory::create();

    $helper = $this->getHelper('question');

    try {
      $this->query->createRandomizationQuery();
    } catch (\Exception $e) {
      $output->writeln($e->getMessage());
    }

    foreach ($this->generators as $generator) {
      (new $generator(
        $this->db,
        $this->data,
        $this->query,
        $this->faker,
        $helper,
        $input,
        $output
      ))->generate();
    }

    $this->generateChat($helper, $input, $output);

    return Command::SUCCESS;
  }

  private function generateChat(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
  {
    $default  = 1000;
    $failures = 0;

    if (!$input->getOption(self::NON_INTERACTIVE)) {
      $question = new Question("How many chat messages would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($input, $output, $question);
    }

    if (!is_numeric($count)) {
      $count = $input->getOption('count') ?? $default;
    }

    $progressBar = new ProgressBar($output, (int)$count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      if (!$this->db->insert('chat', [
        'member_id' => $this->query->getRandomMemberId(),
        'chat'      => $this->faker->realTextBetween(50, 400),
      ])) {
        $failures++;
      };

      $progressBar->advance();
    }

    $progressBar->finish();

    $successCount = $count - $failures;
    $output->writeln("\nSuccessfully generated {$successCount} chat messages out of {$count} requested.");
  }
}
