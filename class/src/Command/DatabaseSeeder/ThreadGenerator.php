<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use PgBoard\PgBoard\Command\DatabaseSeeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\Question;

class ThreadGenerator extends DataGenerator
{
  public function generate(): void
  {
    $this->generateThreads();
    $this->generateReplies();
  }

  private function generateThreads(): void {
    $default = 1000;

    if (!$this->input->getOption(DatabaseSeeder::NON_INTERACTIVE)) {
      $question = new Question("How many threads would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($this->input, $this->output, $question);
    }

    if (!is_numeric($count)) {
      $count = $this->input->getOption('count') ?? $default;
    }

    $failures    = 0;
    $progressBar = new ProgressBar($this->output, (int)$count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      try {
        $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();
        $member = $this->query->getRandomMember();

        ob_start();
        $result = $this->data->thread_insert([
          'name'    => $member['name'],
          'pass'    => DatabaseSeeder::TEST_PASSWORD,
          'subject' => $this->faker->text(),
          'body'    => $this->faker->paragraphs(rand(1, 10), true),
        ]);

        ob_end_clean();
      } catch (\Throwable $e) {
        $failures++;
        $this->output->writeln($e->getMessage());
        continue;
      } finally {
        $progressBar->advance();
      }
    }

    $progressBar->finish();

    $this->output->writeln(
      sprintf(
        "\nSuccessfully generated %d new threads out of %d requested.",
        $count - $failures,
        $count,
      )
    );
  }

  private function generateReplies(): void
  {
    $default = 1000;

    if (!$this->input->getOption(DatabaseSeeder::NON_INTERACTIVE)) {
      $question = new Question("How many thread replies would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($this->input, $this->output, $question);
    }

    $failures = 0;

    if (!is_numeric($count)) {
      $count = $this->input->getOption('count') ?? $default;
    }

    $progressBar = new ProgressBar($this->output, (int)$count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();

      $this->data->thread_post_insert(
        [
          'thread_id' => $this->query->getRandomThreadId(),
          'body'      => $this->faker->paragraphs(rand(1, 10), true),
        ],
        $this->query->getRandomMemberId()
      );

      $progressBar->advance();
    }

    $progressBar->finish();
  }
}
