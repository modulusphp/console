<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Modulus\Console\ModulusCLI;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'serve {port=8000}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to serve your Application';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'serve' => 'Serve the application on the PHP development serve',
    'port' => 'The port to serve the application on'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $port = $input->getOption('port');

    if (!is_numeric($port)) {
      return $output->writeln('Invalid port number');
    }

    $output->writeln('<info>Started modulusPHP Development Serve.</info>');
    $output->writeln('['.date('D M d G:i:s Y').'] Listening on http://localhost:' . $port);
    $output->writeln('Press Ctrl-C to quit.');

    $cmd = passthru('php -S localhost:' . $port . ' -t ' . ModulusCLI::$appdir . DIRECTORY_SEPARATOR . ModulusCLI::$approot);
    $output->writeln($cmd);
  }

}