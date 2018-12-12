<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearSessions extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'clear:sessions';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to clear all user sessions';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'clear:sessions' => 'Clear all user sessions',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $searchDir = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR;

    $sessions = glob($searchDir . '*.*.txt');

    if (count($sessions) < 1) {
      return $output->writeln('Nothing to remove');
    }

    foreach($sessions as $session) {
      try {
        Filesystem::delete($session);
      } catch (\Exception $e) {
        return $output->writeln($e->getMessage());
      }
    }

    return $output->writeln('<info>Cleared sessions</info>');
  }
}