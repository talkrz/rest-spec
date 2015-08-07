<?php

namespace RestSpec\Output\ConstraintDescriber;

use Symfony\Component\Validator\Constraint;

class EqualTo
{
    public function describe(Constraint $constraint)
    {
        $output = sprintf('should equal "%s"', $constraint->value);
        return $output;
    }
}
