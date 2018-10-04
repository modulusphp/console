<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Modulus\Console\ModulusCLI;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRule extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:rule {name}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Validation rule';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'make:rule' => 'Create a new validation rule',
    'name' => 'The name of the class'
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

    if ($this->add($name)) {
      return $output->writeln('<info>Rule "' . $name . '" has been successfuly created.</info>');
    }

    return $output->writeln('Rule "' . $name . '" already exists.');
  }

  /**
   * Add new command
   *
   * @param  string  $name
   * @param  string  $class
   * @return boolean
   */
  private function add(string $name) : bool
  {
    $rules = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Rules';
    $rule = $rules . DIRECTORY_SEPARATOR . $name . '.php';
    $namespace = '';

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($rule, 0, strrpos($rule, DIRECTORY_SEPARATOR)));
      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($rules);

    $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR . 'rule_template');
    $content = str_replace('{rule_name}', $name, $content);
    $content = str_replace('{namespace}', $namespace, $content);

    if (file_exists($rule)) {
      return false;
    }
    else {
      file_put_contents($rule, $content);
      return true;
    }
  }

}