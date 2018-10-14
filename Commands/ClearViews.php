<?php

namespace Modulus\Console\Commands;

use Modulus\Console\ModulusCLI;
use Modulus\Support\Filesystem;
use AtlantisPHP\Console\Command;
use Modulus\Scaffolding\Template;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearViews extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'clear:views';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'This command allows you to clear all compiled view files';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'clear:views' => 'Clear all compiled view files',
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $searchDir = ModulusCLI::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
    $extension = config('view.extension');

    $views = glob($searchDir . '*@*' . $extension);

    if (count($views) < 1) {
      return $output->writeln('Nothing to remove');
    }

    foreach($views as $view) {
      try {
        Filesystem::delete($view);
      } catch (\Exception $e) {
        return $output->writeln($e->getMessage());
      }
    }

    return $output->writeln('<info>Cleared views</info>');
  }
}