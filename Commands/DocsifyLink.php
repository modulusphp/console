<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DocsifyLink extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'docsify:link {dest=api-docs}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Sync your documetations folder with a public resources';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'docsify:link' => "Link Docsify's resources documentations to a public resource",
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
    $docs = ModulusCLI::$appdir . 'resources' . DIRECTORY_SEPARATOR . 'docs';
    $dest = ModulusCLI::$appdir . 'public' . DIRECTORY_SEPARATOR . $this->getDest($input->getOption('dest'));

    if (is_dir($docs)) {
      if (!is_dir($dest) || !is_link($dest)) {
        symlink($docs, $dest);

        if (is_link($dest)) {
          return $output->writeln("<info>Successfully synced the documentations with \"{$this->getDest($input->getOption('dest'))}\"</info>");
        }
      } else {
        return $output->writeln('Destination is already in use.');
      }
    }

    return $output->writeln('Docs folder doesn\'t exist');
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