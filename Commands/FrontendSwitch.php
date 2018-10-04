<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FrontendSwitch extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'frontend:switch';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to change the current Frontend framework';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'frontend:switch' => 'Change Frontend framework',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $current  = ModulusCLI::$appdir . 'resources';
    $package  = ModulusCLI::$appdir . 'package.json';
    $lock     = ModulusCLI::$appdir . 'package-lock.json';
    $front    = __DIR__ . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR . 'frontend';

    $helper = $this->getHelper('question');
    $question = new ChoiceQuestion(
      'Which Frontend JavaScript Framework would you like to use?',
      array('vue' => 'vue', 'react' => 'react', 'abort' => 'cancel')
    );

    $frontend = $helper->ask($input, $output, $question);

    if ($frontend !== 'abort') {
      if (is_dir($current)) {
        Filesystem::delete($current . DIRECTORY_SEPARATOR . 'js');
      }

      if (file_exists($package)) {
        Filesystem::delete($package);
      }

      if (file_exists($lock)) {
        Filesystem::delete($lock);
      }

      Filesystem::copy($front . DIRECTORY_SEPARATOR . $frontend . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'js', $current . DIRECTORY_SEPARATOR .'js');
      Filesystem::copy($front . DIRECTORY_SEPARATOR . $frontend . DIRECTORY_SEPARATOR . 'package.json', $package);

      return $output->writeln("<info>Successfully swicthed to \"{$frontend}\"</info>");
    }
  }

}