<?php

namespace Modulus\Console\Commands;

use Carbon\Carbon;
use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearLogs extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'clear:logs';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to clear all application logs';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'clear:logs' => 'Clear all logs',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $searchDir = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;

    $logs = glob($searchDir . '*.log');

    if (count($logs) < 1) {
      return $output->writeln('Nothing to remove');
    }

    foreach($logs as $log) {
      try {
        Filesystem::delete($log);
      } catch (\Exception $e) {
        return $output->writeln($e->getMessage());
      }
    }

    return $output->writeln('<info>Cleared logs</info>');
  }
}