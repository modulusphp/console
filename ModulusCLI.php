<?php

namespace Modulus\Console;

use Exception;
use AtlantisPHP\Console\Application;

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
    return new Application('modulusPHP Developer Environment');
  }
}