<?php

namespace ModulusPHP\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MakeRuleCommand extends Command
{
  protected $commandName = 'make:rule';
  protected $commandDescription = "Create a new validation rule";

  protected $commandArgumentName = "name";
  protected $commandArgumentDescription = "The name of the class.";

  protected function configure()
  {
    $this
      ->setName($this->commandName)
      ->setDescription($this->commandDescription)
      ->addArgument(
        $this->commandArgumentName,
        InputArgument::REQUIRED,
        $this->commandArgumentDescription
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $name = $input->getArgument($this->commandArgumentName);
    $this->checkFolder();

    $rule = "<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ".$name." implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  " . '$attribute' . "
   * @param  mixed  " . '$value' . "
   * @return bool
   */
  public function passes(" . '$attribute, $value' . ")
  {
    //
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'The validation error message.';
  }
}";

    if ($name != null || $name != '') {
      if (file_exists('app/Rules/'. $name.'.php')) {
        $output->writeln('Rule already exists!');
      }
      else {
        file_put_contents('app/Rules/'. $name.'.php', $rule);
        $output->writeln($name.' was successfully created!');
      }
    }
    else {
      $output->writeln('Specify rule name!');
    }
  }

  private function checkFolder()
  {
    if (is_dir('app/Rules') === false) {
      mkdir('app/Rules', 0777, true);
    }
  }
}