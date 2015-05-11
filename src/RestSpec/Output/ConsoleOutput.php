<?php
namespace RestSpec\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Indents multiline value
 *
 * @param $value
 * @param int $indents
 * @return string
 */
function indentValue($value, $indents = 0)
{
    $tabs = str_repeat("\t", $indents);
    $output = $tabs . str_replace("\n", "\n" . $tabs, $value);

    return $output;
}

class ConsoleOutput
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ConstraintDescriber
     */
    protected $constraintDescriber;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output, ConstraintDescriber $constraintDescriber)
    {
        $this->output = $output;
        $this->constraintDescriber = $constraintDescriber;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param \Exception $e
     * @param int $errorCode
     */
    public function errorHandler(\Exception $e, $errorCode = 1)
    {
        do {
            $this->output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        } while($e = $e->getPrevious());

        exit($errorCode);
    }

    /**
     * @return ConstraintDescriber
     */
    public function getConstraintDescriber()
    {
        return $this->constraintDescriber;
    }

    /**
     * Format array for print in console
     *
     * @param array $array
     * @param int $indents
     * @return string
     */
    public function formatArray(array $array, $indents = 0)
    {
        $output = "<info>" . var_export($array, true) . "</info>";

        if ($indents) {
            $output = indentValue($output, $indents);
        }

        return $output;
    }
}
