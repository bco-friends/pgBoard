<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use PgBoard\PgBoard\Command\DatabaseSeeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\Question;

class MemberGenerator extends DataGenerator
{
  public function generate(): void
  {
    $default = 1000;
    $failures = 0;

    if (!$this->input->getOption(DatabaseSeeder::NON_INTERACTIVE)) {
      $question = new Question("How many members would you like to generate? (Default: {$default}): ");
      $count    = $helper->ask($this->input, $this->output, $question);
    }

    if (!is_numeric($count)) {
      $count = $this->input->getOption('count') ?? $default;
    }

    $this->output->writeln(
      sprintf(
        "\nAttempting to generate %d members...",
        $count
      )
    );

    $progressBar = new ProgressBar($this->output, (int)$count);
    $progressBar->start();

    for ($i = 0; $i < $count; $i++) {
      $result = $this->db->insert(
        'member',
        [
          'name'         => $this->faker->userName(),
          'email_signup' => $this->faker->safeEmail(),
          'pass'         => md5(DatabaseSeeder::TEST_PASSWORD),
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

    $progressBar->finish();

    $this->output->writeln(
      sprintf(
        "\nSuccessfully generated %d member records.",
        $count - $failures,
      )
    );
  }
}
