<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftSeeder extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:seeder {name} {table=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Create a new seeder class';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:seeder' => 'Create a new seeder class',
    'name' => 'The name of the seeder',
    'table' => 'The name of the table, the seeder should run on'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name = strtolower($input->getArgument('name'));
    $table = $input->getOption('table');

    $class = implode('', array_map('ucfirst', explode('_', $name)));

    if ($this->add($name, $class, $table)) {
      return $output->writeln('<info>Seeder "' . $class . '" has been successfully created.</info>');
    }

    return $output->writeln('Seeder "' . $class . '" already exists.');
  }

  /**
   * Add asset
   *
   * @param  string  $name
   * @param  string  $class
   * @param  string  $table
   * @return boolean
   */
  private function add(string $name, string $class, string $table = null) : bool
  {
    $seeds = ModulusCLI::$appdir . 'database' . DIRECTORY_SEPARATOR . 'seeds';
    $seed = $seeds . DIRECTORY_SEPARATOR . $name . '.php';

    ModulusCLI::_dir($seeds);

    $content = Template::asset('seeder_template');
    $content = str_replace('{name}', $class, $content);
    $content = str_replace('{table_name}', $table, $content);

    if (file_exists($seed)) {
      return false;
    }
    else {
      file_put_contents($seed, $content);
      return true;
    }
  }
}
