<?php

namespace RestSpec\Output\ConstraintDescriber;

use Symfony\Component\Validator\Constraint;

class GreaterThan
{
    public function describe(Constraint $constraint)
    {
        $output = 'should by greater than: ' . $constraint->value;

        return $output;
    }
}
