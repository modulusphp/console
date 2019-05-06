<?php

namespace Modulus\Console\Commands;

use Carbon\Carbon;
use AtlantisPHP\Console\Command;
use Modulus\Hibernate\Queue\Commands\Listener;
use Symfony\Component\Console\Input\InputInterface;
use Modulus\Hibernate\Queue\Command as QueueCommand;
use Symfony\Component\Console\Output\OutputInterface;

class QueueListen extends Command
{
  use Listener;
  use QueueCommand;

  /**
   * Exit status
   *
   * @var boolean
   */
  protected $keepRunning = true;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queue:listen {--process=1000} {--timeout=}';

  /**
   * Job status
   *
   * @var bool
   */
  protected $passed = true;

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Listen to a given queue';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'queue:listen' => 'Listen to a given queue',
    'process' => 'Number of queues that should be processed every minute',
    'timeout' => 'Maximum run time for a queue'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    /**
     * Get options
     */
    $limit   = $this->getLimit($input);
    $timeout = $this->getTimeOut($input);

    /**
     * Create timestamp
     */
    $time    = Carbon::now();

    /**
     * Run or restart queue worker every 10 seconds.
     */
    while($this->keepRunning)
      /**
       * Wait for new jobs
       */
      $this->start($limit, $timeout, $output);

      /**
       * Restart after 10 seconds
       */
      if ($time->diffInSeconds() > 9) {
        $this->keepRunning = false;
      }
  }
}
