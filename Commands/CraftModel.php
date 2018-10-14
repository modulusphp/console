<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftModel extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:model {name} {controller=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a Eloquent model';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:model' => 'Create a new Eloquent model class',
    'name' => 'The name of the class',
    'controller' => 'Create a new controller for the model',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $model = $input->getArgument('name');
    $controller = $input->getOption('controller');

    if ($controller == null || $controller == '') {
      if ($this->add($model, 'model') == true) {
        return $output->writeln('<info>Model "' . $model . '" has been successfuly created.</info>');
      }

      return $output->writeln('Model "' . $model . '" or Group "' . $model . 'Group" already exists.');
    }

    if ($this->add($model, 'model', true)) {
      return $output->writeln('Model "' . $model . '" or Group "' . $model . 'Group" already exists.');
    }
    else if ($this->add($controller, 'controller', true)) {
      return $output->writeln('Controller "' . $controller . '" already exists.');
    }
    else {
      $this->add($model, 'model');
      $this->add($controller, 'controller');
      return $output->writeln('<info>Model "' . $model . '" has been successfuly created with Controller "' . $controller . '".</info>');
    }
  }

  /**
   * Add new command
   *
   * @param  string  $name
   * @param  string  $class
   * @return boolean
   */
  private function add(string $name, string $type, bool $verify = false) : bool
  {
    $models = ModulusCLI::$appdir . 'app';
    $groups = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Groupables';
    $controllers = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers';

    $modelcontent = Template::asset('model_template');
    $groupcontent = Template::asset('group_template');
    $controllercontent = Template::asset('controller_template');

    $model = $models . DIRECTORY_SEPARATOR . $name . '.php';
    $group = $groups . DIRECTORY_SEPARATOR . $name . 'Group.php';
    $controller = $controllers . DIRECTORY_SEPARATOR . $name . '.php';

    $namespace = '';

    if ($verify) {
      if ($type == 'model' && (file_exists($model) || file_exists($group))) return true;
      if ($type == 'controller' && file_exists($controller)) return true;

      return false;
    }

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($model, 0, strrpos($model, DIRECTORY_SEPARATOR)));
      ModulusCLI::_dir(substr($group, 0, strrpos($group, DIRECTORY_SEPARATOR)));

      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($models);
    ModulusCLI::_dir($groups);
    ModulusCLI::_dir($controllers);

    switch ($type) {
      case 'model':
        if (file_exists($model)) return false;

        $modelcontent = str_replace('{model_name}', $name, $modelcontent);
        $modelcontent = str_replace('{namespace}', $namespace, $modelcontent);
        $groupcontent = str_replace('{model_name}', $name, $groupcontent);
        $groupcontent = str_replace('{namespace}', $namespace, $groupcontent);

        file_put_contents($model, $modelcontent);
        file_put_contents($group, $groupcontent);
        return true;
        break;

      case 'controller':
        if (file_exists($controller)) {
          return false;
        }

        $controllercontent = str_replace('{namespace}', $namespace, $controllercontent);
        $controllercontent = str_replace('{controller_name}', $name, $controllercontent);
        file_put_contents($controller, $controllercontent);

        $this->cleanController($controller, $namespace);

        return true;
        break;

      default:
        return true;
        break;
    }
  }

  /**
   * cleanController
   *
   * @param string $controller
   * @param string $class
   * @return void
   */
  private function cleanController($controller, $class)
  {
    if ($class == '') {
      $contents = str_replace('

use App\Http\Controllers\Controller;', '', file_get_contents($controller));

      return file_put_contents($controller, $contents);
    }
  }
}