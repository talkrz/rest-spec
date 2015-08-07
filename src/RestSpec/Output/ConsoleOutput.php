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

/**
 * Format simple box of text
 *
 * @param  string   $text     a text to display
 * @param  callable $decorate optional line decorate callback
 * @param  integer $indents   indent (left margin) of the box
 * @return string
 */
function textBox($text, callable $decorate = null, $indents = 0)
{
    $padding = [1, 10, 1, 2];
    $maxWith = 100;
    $lines = explode(PHP_EOL, $text);

    for($i = 0; $i < $padding[0]; ++$i) {
        array_unshift($lines, '');
    }
    for($i = 0; $i < $padding[2]; ++$i) {
        array_push($lines, '');
    }

    $width = max(array_map(function($v) { return strlen($v); }, $lines));
    $width = min($width, $maxWith);

    $formatted = '';

    $linesWrapped = [];
    foreach($lines as $line) {
        if (strlen($line) > $maxWith) {
            $wrappedLine = wordwrap($line, $maxWith, "\n", true);
            $sublines = explode("\n", $wrappedLine);
            foreach($sublines as $subline) {
                $linesWrapped[] = $subline;
            }

        } else {
            $linesWrapped[] = $line;
        }
    }

    foreach($linesWrapped as $line) {
        $line = str_repeat(' ', $padding[3]) .
            str_pad($line, $width, ' ') .
            str_repeat(' ', $padding[1]);

        if ($decorate) {
            $line = $decorate($line);
        }
        $line = indentValue($line, $indents);
        $formatted .= $line . PHP_EOL;
    }

    return $formatted;
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
