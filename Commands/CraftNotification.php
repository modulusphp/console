<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CraftNotification extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'craft:notification {name}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to create a notification';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'craft:notification' => 'Create a new notification',
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
      return $output->writeln('<info>Notification "' . $name . '" has been successfully created.</info>');
    }

    return $output->writeln('Notification "' . $name . '" already exists.');
  }

  /**
   * Add asset
   *
   * @param  string  $name
   * @return boolean
   */
  private function add(string $name) : bool
  {
    $notifications = ModulusCLI::$appdir . 'app' . DIRECTORY_SEPARATOR . 'Notifications';
    $notification = $notifications . DIRECTORY_SEPARATOR . $name . '.php';
    $namespace = '';

    if (substr_count($name, '/') > 0) {
      ModulusCLI::_dir(substr($notification, 0, strrpos($notification, DIRECTORY_SEPARATOR)));
      $namespace = substr($name, 0, strrpos($name, DIRECTORY_SEPARATOR));
      $name = str_replace($namespace . DIRECTORY_SEPARATOR, '', $name);

      $namespace = '\\' . str_replace('/', '\\', $namespace);
    }

    ModulusCLI::_dir($notifications);

    $content = Template::asset('notification_template');
    $content = str_replace('{notification_name}', $name, $content);
    $content = str_replace('{namespace}', $namespace, $content);

    if (file_exists($notification)) {
      return false;
    }
    else {
      file_put_contents($notification, $content);
      return true;
    }
  }
}
