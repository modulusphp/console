<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Modulus\Console\ModulusCLI;
use Modulus\Framework\Migration;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Input\InputInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'migrate {name} {action=up}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create Database tables';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'migrate' => 'Run a migration',
    'name' => 'The name of the migration',
    'action' => 'The action, the migration will take <info>[options: "up", "down"]</info>'
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
    $action = $input->getOption('action');

    $migrationFile = ModulusCLI::$appdir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . '0000_00_00_00_00_00_migrations.php';

    require $migrationFile;

    if (Capsule::schema()->hasTable('migrations') == false) {
      if (file_exists($migrationFile)) {
        call_user_func(['Migrations', 'up']);
      }
      else {
        return $output->writeln('<error>The migrations file is missing</error>');
      }
    }

    $migrationsDir = ModulusCLI::$appdir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

    if ($name == 'all') {
      $succesful = [];

      foreach(glob($migrationsDir.'*.php') as $migration) {
        $migrationPath = substr($migration, strrpos($migration, '/') + 1);
        $className = substr($this->className(substr($migration, strrpos($migration, '/') + 1)), 0, -4);

        if ($className != 'Migrations') {
          require $migration;

          $migrationResponse = $this->migrateAll($migrationPath, $className, $action);
          if ($migrationResponse != 0 || $migrationResponse != 'Couldn\'t migrate. See log for more information') {
            $succesful[] = $migrationPath;
          }
        }

      }

      if (count($succesful) == 0) {
        return $output->writeln('Nothing to migrate');
      }

      foreach($succesful as $succesfulMigration) {
        $output->writeln('<info>"' . substr($succesfulMigration, 0, -4).'" was successful.</info>');
      }

      return;
    }

    $migrationFile = $migrationsDir . $name . '.php';

    if (file_exists($migrationFile)) {
      require $migrationFile;

      $className = $this->className(substr($name, strrpos($name, '/') + 1));

      if ($className != 'Migrations') {
        $migrationResponse = $this->migrateAll($name.'.php', $className, $action);
        if ($migrationResponse != 0 || $migrationResponse != 'Couldn\'t migrate. See log for more information') {
          return $output->writeln('<info>"' . $name.'" was successful.</info>');
        }
        else {
          return $output->writeln('Nothing to migrate');
        }
      }
    }
    else {
      $output->writeln('<error>"'.$migrationFile.'" migration file does not exist</error>');
    }
  }

  /**
   * migrateAll
   *
   * @param string $migrationFile
   * @param string $name
   * @param string $action
   * @return void
   */
  private function migrateAll(string $migrationFile, string $name, string $action)
  {
    $title =  substr($migrationFile, 0, -4);
    $migration = Migration::where('title', $title)->first();

    if (strtolower($action == 'drop' ? 'down' : $action) == 'down') {
      if ($migration != null) {
        try {
          call_user_func([$name, 'down']);
        }
        catch (Exception $e) {
          Log::error($e);
          return '<error>Couldn\'t migrate. See log for more information</error>';
        }

        $migration->delete();
        return '<info>Migration was successful</info>';
      }
      else {
        return 0;
      }
    }
    else if (strtolower($action) == 'up'){
      if ($migration == null) {
        try {
          call_user_func([$name, 'up']);
        }
        catch (Exception $e) {
          Log::error($e);
          return '<error>Couldn\'t migrate. See log for more information</error>';
        }

        Migration::create([
          'title' => $title
        ]);

        return '<info>Migration was successful</info>';
      }
      else {
        return 0;
      }
    }
  }

  /**
   * Get class name
   *
   * @param  string $fullname
   * @return string $class
   */
  private function className(string $fullname) : string
  {
    $m = explode( '_', $fullname);
    $date = $m[0] . '_' . $m[1] . '_' . $m[2] . '_' . $m[3] . '_' . $m[4] . '_' . $m[5] . '_';

    $class = str_replace($date, '', $fullname);
    $class = implode('', array_map('ucfirst', explode('_', $class)));
    return $class;
  }
}