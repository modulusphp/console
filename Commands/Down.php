<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Down extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'down {message=} {allow=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Put the application into maintenance mode';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'down' => 'Put the application into maintenance mode',
    'message' => 'The message for the maintenance mode',
    'allow' => 'IP or networks allowed to access the application while in maintenance mode. <info>(multiple values allowed)</info>'
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

    $message = $input->getOption('message');
    $allow   = $input->getOption('allow') !== null ? $input->getOption('allow') : "[]";

    $downcontent = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR . 'down_template');

    ModulusCLI::_dir($downLocal);

    if (str_contains($allow, ',')) {
      $allow = str_replace(',', '","', $allow);
      $allow = '["' . $allow . '"]';
    } elseif ($allow == "[]") {
      $allow = "[]";
    } else {
      $allow = '["' . $allow . '"]';
    }

    $downcontent = str_replace('{time}', time(), $downcontent);
    $downcontent = str_replace('{message}', $message == null ? 'null' : "\"{$message}\"", $downcontent);
    $downcontent = str_replace('{allowed}', $allow, $downcontent);

    file_put_contents($down, $downcontent);

    if (file_exists($down)) {
      return $output->writeln('<info>Application is now in maintenance mode.</info>');
    }

    $output->writeln('Failed to put application in maintenance mode.');
  }

}