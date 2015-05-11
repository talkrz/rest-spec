<?php

namespace RestSpec\Output;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConstraintDescriber
{
    /**
     * This class is work in progress
     *
     * @todo: this gigantic if..else should be refactored to multiple describer classes
     * @todo: also missing constraints should be added
     *
     * @param Constraint $constraint
     * @return string
     */
    public function describe(Constraint $constraint)
    {
        $output = '';

        if ($constraint instanceof All) {
            $output .= 'Each of results ';
            foreach ($constraint->constraints as $nestedConstraint) {
                $output .= $this->describe($nestedConstraint);
            }
        } elseif ($constraint instanceof Collection) {
            $output .= 'should be an array with following properties:' . PHP_EOL;

            $nestedOutput = '';
            foreach ($constraint->fields as $fieldName => $fieldConstrain) {
                $nestedOutput .= "<comment>" . $fieldName . "</comment> ";

                if ($fieldConstrain->constraints) {
                    foreach ($fieldConstrain->constraints as $nestedConstraint) {
                        $nestedOutput .= $this->describe($nestedConstraint) . PHP_EOL;
                    }
                } else {
                    $nestedOutput .= '[no constraints]' . PHP_EOL;
                }
            }

            $output .= indentValue($nestedOutput, 1);

        } elseif ($constraint instanceof NotBlank) {
            $output .= 'is required';

        } elseif ($constraint instanceof GreaterThan) {
            $output .= 'should by greater than: ' . $constraint->value;
        } elseif ($constraint instanceof Length) {
            $output .= 'should be ';
            if ($constraint->min) {
                $output .= 'at least ' . $constraint->min . ' characters long';
            }
            if ($constraint->max) {
                if ($constraint->min) {
                    $output .= ' and ';
                }
                $output .= 'no longer than ' . $constraint->max . ' characters';
            }
        } else {
            $output .= get_class($constraint);
        }

        return $output;
    }
}
