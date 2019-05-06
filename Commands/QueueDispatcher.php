<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Console\Command;
use Modulus\Hibernate\Queue\Commands\Dispatcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueDispatcher extends Command
{
  use Dispatcher;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queue:dispatch {id=}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'Process a single job';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'queue:dispatch' => 'Process a single job',
    'id' => 'Queue id'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->start($input);
  }

}
