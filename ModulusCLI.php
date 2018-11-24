<?php

namespace Modulus\Console;

use Exception;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Application;
use Modulus\Framework\Application as App;

class ModulusCLI
{
  /**
   * Application root directory
   *
   * @var string $appdir
   */
  public static $appdir;

  /**
   * Application public directory
   *
   * @var string $approot
   */
  public static $approot;

  /**
   * Commands location.
   *
   * @return string
   */
  public static function config() : string
  {
    return __DIR__ . DIRECTORY_SEPARATOR .  'Commands';
  }

  /**
   * Create folder it doesn't exist.
   *
   * @param  string $folder
   * @return bool
   */
  public static function _dir(string $folder) : bool
  {
    if (is_dir($folder)) {
      return true;
    }

    mkdir($folder, 0777, true);
    return false;
  }

  /**
   * Display an error if something happened
   *
   * @param Exception $e
   */
  public static function fails(Exception $e) : void
  {
    echo "\033[31mModulusCLI failed to execute command.\033[0m\n";
    die($e->getMessage() . "\n");
  }

  /**
   * Boot the modulusPHP CLI
   *
   * @return object
   */
  public static function boot() : object
  {
    ModulusCLI::$appdir = config('app.dir');
    ModulusCLI::$approot = config('app.root');

    $app = new Application('Modulus Craftsman', (new ModulusCLI)->getVersion());
    $app->load(ModulusCLI::config());

    Self::autoload_plugins(true, $app);

    return $app;
  }

  /**
   * Get application version from composer file
   *
   * @return string
   */
  public function getVersion()
  {
    $composerJson = config('app.dir') . 'composer.json';

    if (file_exists($composerJson)) {
      $composer = json_decode(file_get_contents($composerJson, true));

      $version  = isset($composer->version) ? $composer->version : '1';
      $require  = isset($composer->require) ? (array)$composer->require : false;

      if (!is_array($require)) return "{$version} (1)";

      if (isset($require['modulusphp/framework'])) {
        return $version . " ({$require['modulusphp/framework']})";
      } else {
        return "{$version} (1)";
      }
    }

    return '1';
  }

  /**
   * autoload_plugins
   *
   * @param bool $isConsole
   * @return void
   */
  public static function autoload_plugins(bool $isConsole, $craftsman)
  {
    $plugins = config('app.plugins');

    if (env('DEV_AUTOLOAD_PLUGINS') == true) {
      foreach($plugins as $plugin => $class) {
        $class::console((object)array_merge(App::prototype($isConsole), ['craftsman' => $craftsman]));
      }
    }
  }
}
