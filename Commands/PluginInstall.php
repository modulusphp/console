<?php

namespace Modulus\Console\Commands;

use ReflectionClass;
use Modulus\Utility\Plugin;
use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Modulus\Framework\Plugin\Validate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginInstall extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'plugin:install {--class=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Verify and install a new plugin';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'plugin:install' => 'Verify and install a new plugin'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    return $this->install($input, $output);
  }

  /**
   * Install plugin
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return void
   */
  private function install(InputInterface $input, OutputInterface $output)
  {
    $plugin     = $this->getPlugin($input);

    $pluginInfo = $this->getInfo($plugin);

    if (Validate::check($plugin, $pluginInfo)) {

      $package = $plugin::PACKAGE;
      $version = $plugin::VERSION;

      if (config('app.plugins') && in_array($pluginInfo->name, config('app.plugins'))) {
        return $output->writeln("<error>{$package} is a registered plugin. Aborting...</error>");
      }

      $output->writeln("<info>Installing: {$package} {$version}</info>");

      if (!$this->addConfig($plugin, $pluginInfo)) {
        throw new \Exception('Could not add config file');
      }

      if (!$this->addMigration($plugin, $pluginInfo)) {
        throw new \Exception('Could not add migration file');
      }

      return $output->writeln("<info>Installed: {$package} {$version}. Append \"{$pluginInfo->name}::class\" in your plugins</info>");

    }

    $output->writeln("<error>Failed: {$package} {$version}</error>");
  }

  /**
   * Get plugin instance
   *
   * @param InputInterface $input
   * @return string
   */
  private function getPlugin(InputInterface $input) : Plugin
  {
    $class = $input->getOption('class');

    if (!class_exists($class)) {
      throw new \Exception("Plugin {$class} does not exist");
    }

    $plugin = new $class;

    if (!$plugin instanceof Plugin) {
      throw new \Exception("{$class} is not a Modulus Plugin");
    }

    return $plugin;
  }

  /**
   * Get plugin information
   *
   * @param Plugin $plugin
   * @return ReflectionClass
   */
  private function getInfo(Plugin $plugin) : ReflectionClass
  {
    return new ReflectionClass($plugin);
  }

  /**
   * Add plugin config
   *
   * @param Plugin $plugin
   * @param ReflectionClass $pluginInfo
   * @return bool
   */
  private function addConfig(Plugin $plugin, ReflectionClass $pluginInfo) : bool
  {
    $pluginConfig = dirname($pluginInfo->getFileName()) . DS . '..' . DS . 'install' .  DS . 'config.php';

    if (
      array_key_exists('CONFIG', $pluginInfo->getConstants()) &&
      file_exists($pluginConfig)
    ) {
      $appConfig = config('app.dir') . 'config' . DS . $plugin::CONFIG . '.php';

      if (file_exists($appConfig)) return false;

      $pluginConfig = Filesystem::get($pluginConfig);

      return Filesystem::put($appConfig, $pluginConfig);
    }

    return true;
  }

  /**
   * Add plugin migration
   *
   * @param Plugin $plugin
   * @param ReflectionClass $pluginInfo
   * @return bool
   */
  private function addMigration(Plugin $plugin, ReflectionClass $pluginInfo) : bool
  {
    $pluginMigration = dirname($pluginInfo->getFileName()) . DS . '..' . DS . 'install' .  DS . 'migration.php';

    if (
      array_key_exists('MIGRATION', $pluginInfo->getConstants()) &&
      file_exists($pluginMigration)
    ) {
      $name = $plugin::MIGRATION;

      $class = implode('', array_map('ucfirst', explode('_', $name)));

      return $this->add($name, $class, Filesystem::get($pluginMigration));
    }

    return true;
  }

  /**
   * Add asset
   *
   * @param string $name
   * @param string $class
   * @param string $content
   * @return bool
   */
  private function add(string $name, string $class, string $content) : bool
  {
    $migrations = ModulusCLI::$appdir . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    $date = date('Y_m_d_H_i_s');
    $migration = $migrations . DIRECTORY_SEPARATOR . $date . '_' . $name . '.php';

    ModulusCLI::_dir($migrations);

    if (file_exists($migration)) {
      return false;
    }

    return file_put_contents($migration, $content);
  }
}
