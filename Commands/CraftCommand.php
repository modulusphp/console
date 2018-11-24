<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:command {name}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a command';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:command' => 'Create a new Craftsman command',
    'name' => 'The name of the new command'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name = $input->getArgument('name');
    $class = implode('', array_map('ucfirst', explode('_', str_replace(':', '_', $name))));

    if ($this->add($name, $class)) {
      return $output->writeln('<info>Command "' . $class . '" has been successfully created.</info>');
    }

    return $output->writeln('Command "' . $class . '" already exists.');
  }

  /**
   * Add new command
   *
   * @param  string  $name
   * @param  string  $class
   * @return boolean
   */
  private function add(string $name, string $class) : bool
  {
    $commands = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Console' . DIRECTORY_SEPARATOR . 'Commands';
    $raw = str_replace(' ', '_', $name);
    $command = $commands . DIRECTORY_SEPARATOR . $class . '.php';

    ModulusCLI::_dir($commands);

    $content = Template::asset('command_template');
    $content = str_replace('{clean_command_name}', $class, $content);
    $content = str_replace('{command_name}', $raw, $content);

    if (file_exists($command)) {
      return false;
    }
    else {
      file_put_contents($command, $content);
      return true;
    }
  }
}
