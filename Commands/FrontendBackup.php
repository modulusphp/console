<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FrontendBackup extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'frontend:backup';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to backup your Frontend';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'frontend:backup' => 'Create a backup of your current Frontend',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $frontend;

    $current = ModulusCLI::$appdir . 'resources' . DIRECTORY_SEPARATOR . 'js';
    $backups = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'frontend';
    $package = ModulusCLI::$appdir . 'package.json';
    $lock    = ModulusCLI::$appdir . 'package-lock.json';

    if (file_exists($package)) {
      $contents = json_decode(file_get_contents($package), true);

      foreach ($contents['devDependencies'] as $key => $value) {
        if ($key == 'vue' || $key == 'react') $frontend = $key;
      }
    }

    $name    = "{$frontend}_" . date('Y_m_d_H_i_s');
    $backup  = ModulusCLI::$appdir .  'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . $name;

    ModulusCLI::_dir($backups);
    ModulusCLI::_dir($backup);

    if (is_dir($backup)) {
      copy($package, $backup . DIRECTORY_SEPARATOR . 'package.json');

      if (file_exists($lock)) {
        copy($lock, $backup . DIRECTORY_SEPARATOR . 'package-lock.json');
      }

      Filesystem::copy($current, $backup . DIRECTORY_SEPARATOR . 'js');
      $output->writeln("<info>Creating backup \"{$name}\"...</info>");
      $output->writeln("<info>Successfully created a \"{$frontend}\" backup</info>");
    } else {
      $output->writeln('<info>Could not backup your current frontend setup</info>');
    }

  }

}