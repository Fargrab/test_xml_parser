<?php

declare(strict_types=1);

namespace App\PhpStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\ClassStringType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\VerbosityLevel;

/**
 * Правило проверяет сравнение чисел со строками (т.к. в php 8 поменялось поведение)
 *
 * @see https://github.com/orklah/psalm-insane-comparison
 * @see https://github.com/phpstan/phpstan-src/pull/423
 *
 * @codeCoverageIgnore
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\BinaryOp>
 */
class InsaneComparisonRule implements \PHPStan\Rules\Rule
{
    private bool $treatMixedAsPossibleString;

    public function __construct(bool $treatMixedAsPossibleString = true)
    {
        $this->treatMixedAsPossibleString = $treatMixedAsPossibleString;
    }

    public function getNodeType(): string
    {
        return Node\Expr\BinaryOp::class;
    }

    public function processNode(Node $node, Scope $scope): array // @phpcs:ignore Generic.Metrics.CyclomaticComplexity
    {
        if (
            !$node instanceof Node\Expr\BinaryOp\Equal
            && !$node instanceof Node\Expr\BinaryOp\NotEqual
        ) {
            return [];
        }

        $leftType = $scope->getType($node->left);
        $rightType = $scope->getType($node->right);

        // if ($left_type instanceof ErrorType || $right_type instanceof ErrorType) {
        //     return [];
        // }

        //on one hand, we're searching for literal 0
        $literal0 = new ConstantIntegerType(0);
        $leftContains0 = false;
        $rightContains0 = false;

        if (!$literal0->isSuperTypeOf($leftType)->no()) {
            if (!$this->treatMixedAsPossibleString || !($leftType instanceof MixedType)) {
                //Left type may contain 0
                $intOperand = $leftType;
                $otherOperand = $rightType;
                $leftContains0 = true;
            }
        }

        if (!$literal0->isSuperTypeOf($rightType)->no()) {
            if (!$this->treatMixedAsPossibleString || !($rightType instanceof MixedType)) {
                //Right type may contain 0
                $intOperand = $rightType;
                $otherOperand = $leftType;
                $rightContains0 = true;
            }
        }

        if (!isset($otherOperand, $intOperand)) {
            return [];
        }
        if (!$leftContains0 && !$rightContains0) {
            // Not interested
            return [];
        }
        if ($leftContains0 && $rightContains0) {
            //This is pretty inconclusive
            return [];
        }

        //On the other hand, we're searching for any non-numeric non-empty string
        if ((new StringType())->isSuperTypeOf($otherOperand)->no()) {
            if (!$this->treatMixedAsPossibleString || !($otherOperand instanceof MixedType)) {
                //we can stop here, there's no string in here
                return [];
            }
        }

        $stringOperand = $otherOperand;

        $eligibleInt = null;
        foreach (TypeUtils::flattenTypes($intOperand) as $possiblyInt) {
            if ($possiblyInt instanceof ConstantIntegerType && $possiblyInt->getValue() === 0) {
                $eligibleInt = $possiblyInt;

                break;
            }
            if ($possiblyInt instanceof IntegerRangeType) {
                if ($possiblyInt->isSuperTypeOf(new ConstantIntegerType(0))->no()) {
                    // range doesn't contain 0, not interested
                    continue;
                }
            } elseif ($possiblyInt instanceof IntegerType) {
                // we found a general Int, it may contain 0
                $eligibleInt = $possiblyInt;

                break;
            }
        }

        $eligibleString = null;
        foreach (TypeUtils::flattenTypes($stringOperand) as $possiblyString) {
            if ($possiblyString instanceof ConstantStringType) {
                if (!is_numeric($possiblyString->getValue())) {
                    $eligibleString = $possiblyString;

                    break;
                }

                continue;
            }
            if ($possiblyString instanceof AccessoryNumericStringType) {
                // not interested
                continue;
            }
            if ($possiblyString instanceof StringType && !$possiblyString instanceof ClassStringType) {
                $eligibleString = $possiblyString;

                break;
            }
            if ($this->treatMixedAsPossibleString && $possiblyString instanceof MixedType) {
                $eligibleString = $possiblyString;

                break;
            }
        }

        if ($eligibleInt !== null && $eligibleString !== null) {
            return [
                RuleErrorBuilder::message(sprintf(
                    'Possible Insane Comparison between %s and %s',
                    $scope->getType($node->left)->describe(VerbosityLevel::value()),
                    $scope->getType($node->right)->describe(VerbosityLevel::value())
                ))->line($node->left->getLine())->build(),
            ];
        }

        return [];
    }
}
