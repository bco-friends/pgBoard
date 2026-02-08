<?php
declare(strict_types=1);

namespace PgBoard\PgBoard\Command\DatabaseSeeder;

use DB;
use Data;
use Faker\Generator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class DataGenerator
{
  public function __construct(
    protected readonly DB $db,
    protected readonly Data $data,
    protected readonly Query $query,
    protected readonly Generator $faker,
    protected readonly QuestionHelper $helper,
    protected readonly InputInterface $input,
    protected readonly OutputInterface $output
  ) {}

  abstract public function generate(): void;
}
