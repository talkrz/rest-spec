<?php

namespace RestSpec\Validator;

class Validator
{
    /**
     * List of violations
     *
     * @var array
     */
    protected $violations;

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->violations);
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    public function addViolation($message)
    {
        $this->violations[] = $message;
    }

    public function addViolations($messages)
    {
        if (!is_array($messages)) {
            return;
        }

        foreach($messages as $message) {
            $this->addViolation($message);
        }
    }

    public function reset()
    {
        $this->violations = [];
    }
}
