<?php

namespace Modulus\Console\Commands;

use Modulus\Hibernate\Cache;
use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'clear:cache';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to clear hibernate cache';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'clear:cache' => 'Clear hibernate cache',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $results = Cache::flush();

    if ($results) return $output->writeln('<info>Cleared cache!</info>');

    return $output->writeln('Nothing to clear');
  }
}
