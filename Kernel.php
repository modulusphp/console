<?php

namespace Modulus\Console;

class Kernel
{
  /**
   * Start scheduler
   *
   * @param Schedule $scheduler
   * @return void
   */
  public function run($scheduler)
  {
    $this->schedule($scheduler);
  }
}