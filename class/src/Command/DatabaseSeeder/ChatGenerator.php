<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use PgBoard\PgBoard\Command\DatabaseSeeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\Question;

class ChatGenerator extends DataGenerator
{
  public function generate(): void
  {
    $default  = 1000;
    $failures = 0;

    if (!$this->input->getOption(DatabaseSeeder::NON_INTERACTIVE)) {
      $question = new Question("How many chat messages would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($this->input, $this->output, $question);
    }

    if (!is_numeric($count)) {
      $count = $this->input->getOption('count') ?? $default;
    }

    $progressBar = new ProgressBar($this->output, (int)$count);
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
    $this->output->writeln("\nSuccessfully generated {$successCount} chat messages out of {$count} requested.");
  }
}
