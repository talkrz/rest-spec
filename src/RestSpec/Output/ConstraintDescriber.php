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

        $className = explode('\\', get_class($constraint));
        $className = $className[count($className) - 1];
        $className = 'RestSpec\\Output\\ConstraintDescriber\\' . $className;

        if ($constraint instanceof All) {
            $output .= $this->describeAll($constraint);
        } else if ($constraint instanceof Collection) {
            $output .= $this->describeCollection($constraint);
        } else if (class_exists($className)) {
            $describer = new $className;
            $output .= $describer->describe($constraint);
        } else {
            $output .= get_class($constraint);
        }

        return $output;
    }

    /**
     * Describe collection constraint
     *
     * @param Collection $constraint
     * @return string
     */
    private function describeCollection(Collection $constraint)
    {
        $output = 'should be an array with following properties:' . PHP_EOL;

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

        return $output;
    }

    /**
     * Describe "all" constraint
     *
     * @param All $constraint
     * @return string
     */
    private function describeAll(All $constraint)
    {
        $output = 'Each of results ';
        foreach ($constraint->constraints as $nestedConstraint) {
            $output .= $this->describe($nestedConstraint);
        }

        return $output;
    }
}
