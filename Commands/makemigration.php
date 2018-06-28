<?php

namespace ModulusPHP\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MakeMigrationCommand extends Command
{
  protected $commandName = 'make:migration';
  protected $commandDescription = "Create a new migration class";

  protected $commandArgumentName = "name";
  protected $commandArgumentDescription = "The name of the migration.";

  protected $commandOptionMigration = "action";
  protected $commandMigrationDescription = 'The type of migration.';

  protected function configure()
  {
    $this
      ->setName($this->commandName)
      ->setDescription($this->commandDescription)
      ->addArgument(
        $this->commandArgumentName,
        InputArgument::REQUIRED,
        $this->commandArgumentDescription
      )
      ->addArgument(
        $this->commandOptionMigration,
        InputArgument::OPTIONAL,
        $this->commandMigrationDescription
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name = strtolower($input->getArgument($this->commandArgumentName));
    $type = $input->getArgument($this->commandOptionMigration);
    $table = null;

    $this->checkFolder();

    $date = date('Y_m_d_H_i_s_');
    $migrationFile = 'storage/migrations/'.$date.$name.'.php';

    $class = implode('', array_map('ucfirst', explode('_', $name)));

    if (!file_exists($migrationFile)) {
      if ($type != null) {
        if (0 === strpos($type, 'add_to_')) {
          $table = strtolower(str_replace('add_to_', '', $type));
          $this->addTo($name, $table, $class);
          $output->writeln($name.' was successfully created!');
        }
        else {
          $output->writeln('Not sure what you\'re trying to do.');
        }
      }
      else {
        $this->addTable($name, $class);
        $output->writeln($name.' was successfully created!');
      }
    }
    else {
      $output->writeln('Migration already exists!');
    }
  }

  private function addTo($name, $tableName, $class)
  {
    $date = date('Y_m_d_H_i_s_');
    $migration = '<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class '.$class.'
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Capsule::schema()->table("'.$tableName.'", function ($table) {
      $table->string("'.$name.'");
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Capsule::schema()->table("'.$tableName.'", function ($table) {
      $table->dropColumn("'.$name.'");
    });
  }
}';

    file_put_contents('storage/migrations/'. $date.$name.'.php', $migration);
  }

  private function addTable($name, $class)
  {
    $date = date('Y_m_d_H_i_s_');
    $migration = '<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class '.$class.'
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Capsule::schema()->create("'.$name.'", function ($table) {
            $table->increments("id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists("'.$name.'");
    }
}';

    file_put_contents('storage/migrations/'. $date.$name.'.php', $migration);
  }

  private function checkFolder()
  {
    if (is_dir('storage/migrations') === false) {
      mkdir('storage/migrations', 0777, true);
    }
  }
}