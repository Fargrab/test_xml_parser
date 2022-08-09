<?php

declare(strict_types=1);

namespace App\PhpStan;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Throwable;

/**
 * Правило требует замены `catch (\Exception)` на `catch (\Throwable)`
 *
 * @codeCoverageIgnore
 * @implements Rule<Catch_>
 */
class CatchThrowableInspection implements Rule
{
    public function getNodeType(): string
    {
        return Catch_::class;
    }

    /**
     * @param \PhpParser\Node\Stmt\Catch_ $node
     *
     * @return \PHPStan\Rules\RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $catchClasses = array_map(fn (Node\Name $catch): string => $catch->toString(), $node->types);
        if (\in_array(Exception::class, $catchClasses, true) && !\in_array(Throwable::class, $catchClasses, true)) {
            $message = RuleErrorBuilder::message(sprintf('Catching \Exception::class is PHP 5 legacy. You should always catch \Throwable::class instead.'))
                ->tip('Learn more at https://www.php.net/manual/language.errors.php7.php')
                ->build();

            return [$message];
        }

        return [];
    }
}
