<?php

namespace Modulus\Console\Commands;

use App\Console\Scheduler;
use GO\Scheduler as Runner;
use AtlantisPHP\Console\Command;
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
    $schedule = new Scheduler();
    $schedule->schedule($scheduler);
    $scheduler->run();
  }
}