<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrontendRestore extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'frontend:restore {backup}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to restore a Frontend backup';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'frontend:restore' => 'Restore a backup',
    'backup' => 'The name of the backup',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $current  = ModulusCLI::$appdir . 'resources' . DIRECTORY_SEPARATOR . 'js';
    $package  = ModulusCLI::$appdir . 'package.json';
    $lock     = ModulusCLI::$appdir . 'package-lock.json';
    $backup   = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . $input->getArgument('backup');

    if (!is_dir($backup)) {
      return $output->writeln('This backup does not exist.');
    }

    if (!file_exists($backup . DIRECTORY_SEPARATOR . 'package.json') || !is_dir($backup . DIRECTORY_SEPARATOR . 'js')) {
      return $output->writeln('This backup is not complete.');
    }

    $helper = $this->getHelper('question');
    $question = new ChoiceQuestion(
      'Are you sure, you want to restore this backup?',
      array('yes' => 'y', 'no' => 'n')
    );

    $confirmed = $helper->ask($input, $output, $question) == 'yes' ? true : false;

    if ($confirmed) {
      if (is_dir($current)) {
        Filesystem::delete($current);
      }

      if (file_exists($package)) {
        Filesystem::delete($package);
      }

      if (file_exists($lock)) {
        Filesystem::delete($lock);
      }

      Filesystem::copy($backup . DIRECTORY_SEPARATOR . 'js', $current);
      Filesystem::copy($backup . DIRECTORY_SEPARATOR . 'package.json', $package);
      Filesystem::copy($backup . DIRECTORY_SEPARATOR . 'package-lock.json', $lock);

      return $output->writeln("<info>Successfully restored \"{$input->getArgument('backup')}\"</info>");
    }

    $output->writeln("Could not restore \"{$input->getArgument('backup')}\".");
  }
}
