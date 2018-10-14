<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftMigration extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:migration {name} {table=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Database Migration';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:migration' => 'Create a new migration class',
    'name' => 'The name of the migration',
    'table' => 'The name of the table, the migration should be added to'
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

    if ($table == null || $table == '') {
      if ($this->add($name, $class)) {
        return $output->writeln('<info>Migration "' . $class . '" has been successfuly created.</info>');
      }

      return $output->writeln('Migration "' . $class . '" already exists.');
    }

    if ($this->add($name, $class, $table)) {
      return $output->writeln('<info>Migration "' . $class . '" has been successfuly created.</info>');
    }

    return $output->writeln('Migration "' . $class . '" already exists.');
  }

  /**
   * Add new command
   *
   * @param  string  $name
   * @param  string  $class
   * @return boolean
   */
  private function add(string $name, string $class, string $table = null) : bool
  {
    $migrations = ModulusCLI::$appdir . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    $date = date('Y_m_d_H_i_s');
    $migration = $migrations . DIRECTORY_SEPARATOR . $date . '_' . $name . '.php';

    ModulusCLI::_dir($migrations);

    if ($table != null) {
      $content = Template::asset('migration_add_template');
      $content = str_replace('{migration_class}', $class, $content);
      $content = str_replace('{migration_name}', $name, $content);
      $content = str_replace('{table_name}', $table, $content);
    }
    else {
      $content = Template::asset('migration_template');
      $content = str_replace('{migration_class}', $class, $content);
      $content = str_replace('{migration_name}', $name, $content);
    }

    if (file_exists($migration)) {
      return false;
    }
    else {
      file_put_contents($migration, $content);
      return true;
    }
  }

}