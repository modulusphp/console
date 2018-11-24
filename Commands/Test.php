<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'test';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Run application tests';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'test' => 'Run application tests'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    unset($_SERVER['argv'][1]);

    $phpUnit = new \PHPUnit\TextUI\Command;
    $phpUnit->run($_SERVER['argv'], true);
  }
}
