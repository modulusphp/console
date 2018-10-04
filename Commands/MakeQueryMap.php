<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Modulus\Console\ModulusCLI;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeQueryMap extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:querymap {name} {type}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Query Map';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'make:querymap' => 'Create a new query map',
    'name' => 'The name of the class',
    'type' => 'Type of query map. <info>[options: "model", "group"]</info>'
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
    $type = $input->getArgument('type');

    if (!in_array(strtolower($type), ['model', 'group'])) {
      return $output->writeln('Unknown query map type');
    }

    if ($this->add($name, $type)) {
      return $output->writeln('<info>Query Map "' . $name . '" has been successfuly created.</info>');
    }

    return $output->writeln('Query Map "' . $name . '" already exists.');
  }

  /**
   * Add new command
   *
   * @param  string  $name
   * @param  string  $class
   * @return boolean
   */
  private function add(string $name, string $type) : bool
  {
    $maps = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'QueryMaps';
    $map = $maps . DIRECTORY_SEPARATOR . $name . '.php';
    $namespace = '';

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($map, 0, strrpos($map, DIRECTORY_SEPARATOR)));
      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($maps);

    if (strtolower($type) == 'model') $type = 'map_template';
    if (strtolower($type) == 'group') $type = 'map_group_template';

    $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Assets' . DIRECTORY_SEPARATOR . $type);
    $content = str_replace('{map_name}', $name, $content);
    $content = str_replace('{namespace}', $namespace, $content);

    if (file_exists($map)) {
      return false;
    }
    else {
      file_put_contents($map, $content);
      return true;
    }
  }

}