<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrontendCurrent extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'frontend:current';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command returns the name of the Frontend framework in use';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'frontend:current' => 'Get the name of the current Frontend framework',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $package  = ModulusCLI::$appdir . 'package.json';

    if (file_exists($package)) {
      $key = '';
      $version = '';
      $contents = json_decode(file_get_contents($package), true);

      foreach ($contents['devDependencies'] as $key => $value) {
        if ($key == 'vue' || $key == 'react') $frontend = $key; $version = $value;
      }

      return $output->writeln("<info>{$frontend} {$version}</info>");
    }

    $output->writeln("unknown");
  }

}