<?php

namespace RestSpec\Output\ConstraintDescriber;

use Symfony\Component\Validator\Constraint;

class NotBlank
{
    public function describe(Constraint $constraint)
    {
        $output = 'is required';

        return $output;
    }
}
