<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageLink extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'storage:link {dest=storage}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Create a symbolic link from "public/storage" to "storage/app/public"';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'storage:link' => 'Create a symbolic link from "public/storage" to "storage/app/public"',
    'dest' => 'The destination of the documetations',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $docs = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
    $dest = ModulusCLI::$appdir . 'public' . DIRECTORY_SEPARATOR . $this->getDest($input->getOption('dest'));

    if (is_dir($docs)) {
      if (!is_dir($dest) || !is_link($dest)) {
        symlink($docs, $dest);

        if (is_link($dest)) {
          return $output->writeln("<info>The [public/{$this->getDest($input->getOption('dest'))}] directory has been linked</info>");
        }
      } else {
        return $output->writeln('Destination is already in use.');
      }
    }

    return $output->writeln('Folder doesn\'t exist');
  }

  /**
   * Get destination
   *
   * @param string $dest
   * @return string
   */
  private function getDest(string $dest)
  {
    return substr($dest, 0, 1) == DIRECTORY_SEPARATOR ? substr($dest, 1) : $dest;
  }

}
