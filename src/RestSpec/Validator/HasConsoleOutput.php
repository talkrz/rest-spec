<?php

namespace RestSpec\Validator;

use RestSpec\Output\ConsoleOutput;

trait HasConsoleOutput
{
    /**
     * An output for messages
     *
     * @var ConsoleOutput
     */
    protected $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * @return ConsoleOutput
     */
    public function getOutput()
    {
        return $this->output;
    }
}
