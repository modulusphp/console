<?php

namespace Modulus\Console\Commands;

use App\Console\Kernel;
use GO\Scheduler as Runner;
use AtlantisPHP\Console\Command;
use Modulus\System\Scheduler as SystemScheduler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Schedule extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'schedule:run';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Run the scheduled commands';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'schedule:run' => 'Run the scheduled commands'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $scheduler = new Runner();
    $schedule = new Kernel();
    $schedule->run($scheduler);

    // run application commands
    (new SystemScheduler)->run($scheduler);

    $this->autoload_plugins($scheduler);

    $scheduler->run();
  }

  /**
   * autoload_plugins
   *
   * @param mixed $scheduler
   * @return void
   */
  private function autoload_plugins($scheduler)
  {
    if (env('DEV_AUTOLOAD_PLUGINS') == true) {
      $plugins = config('app.plugins');

      foreach($plugins as $plugin => $class) {
        $class::schedule($scheduler);
      }
    }
  }
}
