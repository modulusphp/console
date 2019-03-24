<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use AtlantisPHP\Console\Command;
use Modulus\Framework\Migration;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seed extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'seed {name} {count=} {locale=}';

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
    'seed' => 'Run a seed',
    'name' => 'The name of the seed',
    'count' => 'The number of rows, the seed will create',
    'locale' => 'The default locale'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name   = strtolower($input->getArgument('name'));
    $count  = (!in_array((int)$input->getOption('count'), ['', null]) ? (int)$input->getOption('count') : null);
    $locale = (!in_array($input->getOption('locale'), ['', null]) ? $input->getOption('locale') : null);

    /**
     * Check if locale is supported before proceeding
     */
    if (
      $locale &&
      !in_array($locale, (is_array(config('faker.locale.supported')) ? config('faker.locale.supported') : []))
    ) {
      return $output->writeln('<info>Locale "'. $locale .'" is not supported.</info>');
    }

    $seeds = ModulusCLI::$appdir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR;

    $seed = $seeds . $name . '.php';

    if (file_exists($seed)) {

      if ($count !== null && !is_int($count)) return $output->writeln('<error>"'.$count.'" is not a real number</error>');

      require $seed;

      $className = $this->className(substr($name, strrpos($name, '/')));

      if (class_exists($className)) {
        $seed = new $className;
        $seed->count  = ($count ?? $seed->count);
        $seed->locale = ($locale ?? $seed->locale);

        ProgressBar::setFormatDefinition('count', 'Processing: %current%/%max%.');

        $progressBar = new ProgressBar($output, $seed->count);
        $progressBar->setFormat('count');
        $progressBar->start();

        $results = $seed->run($progressBar);

        if (!$results) {
          return $output->writeln('<error>Could not seed "' . $name . '"</error>');
        }
      }

      return $output->writeln('<info>"' . $name.'" was successful.</info>');
    }
    else {
      $output->writeln('<error>"'.$seed.'" seed file does not exist</error>');
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
    $class = implode('', array_map('ucfirst', explode('_', $fullname)));
    return $class;
  }
}
