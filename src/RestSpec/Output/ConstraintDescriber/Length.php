<?php

namespace RestSpec\Output\ConstraintDescriber;

use Symfony\Component\Validator\Constraint;

class Length
{
    public function describe(Constraint $constraint)
    {
        $output = 'should be ';
        if ($constraint->min) {
            $output .= 'at least ' . $constraint->min . ' characters long';
        }
        if ($constraint->max) {
            if ($constraint->min) {
                $output .= ' and ';
            }
            $output .= 'no longer than ' . $constraint->max . ' characters';
        }

        return $output;
    }
}
