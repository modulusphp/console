<?php

namespace Modulus\Console\Commands;

use Modulus\Security\Hash;
use AtlantisPHP\Console\Command;
use Modulus\Console\ModulusCLI;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyGenerate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'key:generate {hash=base64} {length=32}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to generate a new key for your Application';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'key:generate' => 'Set the application key',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $hash = $input->getOption('hash');
    $length = $input->getOption('length');
    $key = $this->newKey($hash, $length);

    if ($this->setKey($key)) return $output->writeln("<info>Application key [{$key}] set successfully.</info>");

    $output->writeln("Can't set application key. Please check if .env file exists");
  }

  /**
   * Generate a new key
   *
   * @param string $hash
   * @param int $length
   * @param string $key
   * @return string
   */
  private function newKey($hash, $length, $key = '')
  {
    $generated = $this->hasReq(Hash::secure($length));

    if ($hash == 'base64') {
      $key = 'base64:' . base64_encode($generated);
    } else {
      $key = $hash . ':' . $generated;
    }

    return $key;
  }

  /**
   * Update the old key
   *
   * @param string $key
   * @return bool
   */
  private function setKey($key)
  {
    $env = file_get_contents(config('app.dir') . '.env');

    $new = str_replace('APP_KEY=' . config('app.key'), 'APP_KEY=' . $key, $env);
    if ($env == '' || $env == null) {
      return false;
    }

    if (!file_exists(config('app.dir') . '.env')) return false;

    file_put_contents(config('app.dir') . '.env', $new);
    return true;
  }

  /**
   * Check if key has all the required stuff
   *
   * @param string $generated
   * @return void
   */
  private function hasReq($generated)
  {
    if (in_array(str_split('0123456789'), array_values(str_split($generated)))) {
      $generated = str_shuffle($generated .= 1);
    }

    if (in_array(str_split('abcdefghijklmnopqrstuvwxyz'), array_values(str_split($generated)))) {
      $generated = str_shuffle($generated .= 'b');
    }

    if (in_array(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), array_values(str_split($generated)))) {
      $generated = str_shuffle($generated .= 'B');
    }

    if (in_array(str_split('~!@#$%^&*()_-=+<>/\?;:{}[]|,.'), array_values(str_split($generated)))) {
      $generated = str_shuffle($generated .= '!');
    }

    return $generated;
  }
}