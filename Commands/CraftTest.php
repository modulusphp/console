<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftTest extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:test {name} {unit=false}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Application event';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:test' => 'Create a new test class',
    'name' => 'The name of the class',
    'unit' => 'Create a unit test'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $isUnit = strtolower($input->getOption('unit')) == 'true' ? true : false;

    $name = $input->getArgument('name');

    if ($this->add($name, $isUnit)) {
      return $output->writeln('<info>Test "' . $name . '" has been successfully created.</info>');
    }

    return $output->writeln('Test "' . $name . '" already exists.');
  }

  /**
   * Add asset
   *
   * @param  string  $name
   * @param  boolean $isUnit
   * @return boolean
   */
  private function add(string $name, bool $isUnit) : bool
  {
    $tests = ModulusCLI::$appdir . 'tests' . DIRECTORY_SEPARATOR . ($isUnit ? 'Unit' : 'Feature');
    $test = $tests . DIRECTORY_SEPARATOR . $name . '.php';
    $namespace = '';

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($test, 0, strrpos($test, DIRECTORY_SEPARATOR)));
      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($tests);

    $template = ($isUnit ? 'unit_test_template' : 'feature_test_template');

    $content = Template::asset($template);
    $content = str_replace('{test_name}', $name, $content);
    $content = str_replace('{namespace}', $namespace, $content);

    if (file_exists($test)) {
      return false;
    }
    else {
      file_put_contents($test, $content);
      return true;
    }
  }

}
