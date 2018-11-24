<?php

namespace Modulus\Console\Commands;

use Psy\Shell as PyShell;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Shell extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'shell';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'An interactive shell for modern PHP';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'shell' => 'An interactive shell for modern PHP'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new PyShell)->run();
  }
}
