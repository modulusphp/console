<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'up';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Bring the application out of maintenance mode';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'up' => 'Bring the application out of maintenance mode',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $downLocal = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'framework';
    $down = $downLocal . DIRECTORY_SEPARATOR . 'down';

    if (!file_exists($down)) {
      return $output->writeln('<info>Application is already live.</info>');
    }

    unlink($down);

    if (file_exists($down) == false) {
      return $output->writeln('<info>Application is now live.</info>');
    }

    $output->writeln('Failed to put application in maintenance mode.');
  }
}
