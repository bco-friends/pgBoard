<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use PgBoard\PgBoard\Command\DatabaseSeeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\Question;

class MessageGenerator extends DataGenerator
{
  public function generate(): void
  {
    $default  = 1000;
    $failures = 0;

    if (!$this->input->getOption(DatabaseSeeder::NON_INTERACTIVE)) {
      $question = new Question("How many messages would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($this->input, $this->output, $question);
    }

    if (!is_numeric($count)) {
      $count = $this->input->getOption('count') ?? $default;
    }

    $this->output->writeln(
      sprintf(
        "\nAttempting to generate %d member messages...",
        $count
      )
    );

    $progressBar = new ProgressBar($this->output, (int)$count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      $memberCount  = rand(1, 5);
      $member       = $this->query->getRandomMember();
      $recipientIds = [];

      for ($k = 0; $k < $memberCount; $k++) {
        $recipientIds[] = $this->query->getRandomMemberId();
      }

      if (!in_array($member['id'], $recipientIds, true)) {
        $recipientIds[] = $member['id'];
      }

      $_SERVER['REMOTE_ADDR'] = $this->faker->ipv4();

      ob_start();
      if (!$this->data->message_insert(
        [
          'name'            => $member['name'],
          'pass'            => DatabaseSeeder::TEST_PASSWORD,
          'thread_id'       => $this->query->getRandomThreadId(),
          'subject'         => $this->faker->text(),
          'body'            => $this->faker->paragraphs(rand(1, 10), true),
          'message_members' => implode(',', array_unique(array_filter($recipientIds))),
        ],
        $member['id'],
      )
      ) {
        ob_end_clean();
        $failures++;
        $progressBar->advance();
        continue;
      }

      ob_end_clean();
      $progressBar->advance();
    }

    $this->output->writeln(
      sprintf(
        "\nSuccessfully generated %d messages.",
        $count - $failures
      )
    );

    $progressBar->finish();
  }
}
