<?php

namespace Modulus\Console\Commands;

use AtlantisPHP\Swish\Route;
use AtlantisPHP\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RouteList extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'route:list {method=all}';

  /**
   * The full command description.
   *
   * @var string
   */
  protected $help = 'List all registered routes';

  /**
   * The descriptions of the console commands.
   *
   * @var array
   */
  protected $descriptions = [
    'route:list' => 'List all registered routes'
  ];

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $method = $input->getOption('method');
    $routes = array();

    foreach (Route::$routes as $key => $route) {
      if ($method == 'all') {
        $middleware = implode('|', $route['middleware']);
        array_push($routes, array(implode(',', $route['method']), $route['pattern'], $route['name'], is_string($route['callback']) ? $route['callback'] : '<comment>Closure</comment>', $middleware));
      } else if (str_contains(implode(' ', $route['method']), $method)) {
        $middleware = implode('|', $route['middleware']);
        array_push($routes, array(implode(',', $route['method']), $route['pattern'], $route['name'], is_string($route['callback']) ? $route['callback'] : '<comment>Closure</comment>', $middleware));
      }
    }

    $table = new Table($output);
    $table->setStyle('box');
    $table
        ->setHeaders(array('Method', 'URI', 'Name', 'Action', 'Middleware'))
        ->setRows(
            $routes
        )
    ;
    $table->render();
  }
}