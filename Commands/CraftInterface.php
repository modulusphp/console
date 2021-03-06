<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftInterface extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:interface {name}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Application interface';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:interface' => 'Create a new application interface',
    'name' => 'The name of the interface'
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
      return $output->writeln('<info>Interface "' . $name . '" has been successfully created.</info>');
    }

    return $output->writeln('File "' . $name . '" already exists.');
  }

  /**
   * Add asset
   *
   * @param  string  $name
   * @return boolean
   */
  private function add(string $name) : bool
  {
    $appdir = ModulusCLI::$appdir . 'app';
    $app = $appdir . DIRECTORY_SEPARATOR . $name . '.php';
    $namespace = '';

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($app, 0, strrpos($app, DIRECTORY_SEPARATOR)));
      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($appdir);

    $content = Template::asset('interface_template');
    $content = str_replace('{name}', $name, $content);
    $content = str_replace('{namespace}', $namespace, $content);

    if (file_exists($app)) {
      return false;
    }
    else {
      file_put_contents($app, $content);
      return true;
    }
  }
}
