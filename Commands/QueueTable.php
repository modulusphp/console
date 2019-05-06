<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueTable extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queue:table';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Create a migration for the queue jobs database table';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'queue:table' => 'Create a migration for the queue jobs database table',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name = 'jobs';

    $class = implode('', array_map('ucfirst', explode('_', $name)));

    if ($this->add($name, $class)) {

      return $output->writeln('<info>Jobs migration "' . $class . '" has been successfully created.</info>');

    }

    return $output->writeln('Jobs Migration "' . $class . '" already exists.');
  }

  /**
   * Add asset
   *
   * @param string $name
   * @param string $class
   * @return bool
   */
  private function add(string $name, string $class) : bool
  {
    $migrations = ModulusCLI::$appdir . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    $date = date('Y_m_d_H_i_s');
    $migration = $migrations . DIRECTORY_SEPARATOR . $date . '_' . $name . '.php';

    ModulusCLI::_dir($migrations);

    $content = Template::asset('jobs_migration_template');

    if (file_exists($migration)) {
      return false;
    }

    file_put_contents($migration, $content);
    return true;
  }
}
