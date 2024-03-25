<?php

declare(strict_types=1);

namespace AndreasWolf\PhpstanTrivialAccessors\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * Verifies that a getter that only consists of a single return target the correct property name.
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
class SimpleGetterCorrectPropertyName implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\ClassMethod::class;
    }

    /**
     * @param Node\Stmt\ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$scope->isInClass()) {
            throw new ShouldNotHappenException();
        }

        $methodName = $node->name->name;
        if (!str_starts_with($methodName, 'get')) {
            return [];
        }

        switch (count($node->stmts ?? [])) {
            case 0:
                return [
                    RuleErrorBuilder::message('A method called get* must not be empty, but return a value')
                        ->identifier('getter.empty')
                        ->build(),
                ];

            case 1:
                $statement = $node->stmts[0];
                if (!$statement instanceof Node\Stmt\Return_) {
                    return [
                        RuleErrorBuilder::message('A method called get* should consist of a single return statement')
                            ->identifier('getter.noreturn')
                            ->build(),
                    ];
                }

                $inferredVariableName = lcfirst(substr($methodName, 3));
                $variableInExpression = $statement->expr;
                if (!$variableInExpression instanceof Node\Expr\PropertyFetch) {
                    return [
                        RuleErrorBuilder::message('A method called get* should return a local object variable')
                            ->identifier('setter.noobjectproperty')
                            ->build()
                    ];
                }
                if (!$variableInExpression->name instanceof Node\Identifier) {
                    // The returned expression is something like $this->foo->bar
                    // TODO support this
                    return [];
                }
                $actualVariableName = $variableInExpression->name->name;

                if ($inferredVariableName !== $actualVariableName) {
                    return [
                        RuleErrorBuilder::message(sprintf(
                            'Method %s() should return $%s, but returns $%s',
                            $methodName,
                            $inferredVariableName,
                            $actualVariableName
                        ))
                            ->identifier('setter.wrongproperty')
                            ->build()
                    ];
                }

                return [];

            default:
                // more than one statement => non-trivial method, none of our business
                return [];
        }
    }
}
