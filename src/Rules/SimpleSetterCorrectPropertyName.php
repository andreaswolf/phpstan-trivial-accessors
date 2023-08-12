<?php

declare(strict_types=1);

namespace AndreasWolf\PhpstanTrivialAccessors\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * Verifies that a simple setter that just consists of an assignment targets the correct class property.
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
class SimpleSetterCorrectPropertyName implements Rule
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
        if (!str_starts_with($methodName, 'set')) {
            return [];
        }

        switch (count($node->stmts ?? [])) {
            case 0:
                return [
                    RuleErrorBuilder::message('A method called set* must not be empty, but assign the passed value to a variable')
                        ->identifier('setter.empty')
                        ->build(),
                ];

            case 1:
                $statement = $node->stmts[0];
                if (!$statement instanceof Node\Stmt\Expression) {
                    // TODO add test for this
                    return [
                        RuleErrorBuilder::message('A method called set* should consist of a single assignment')
                            ->identifier('setter.noexpression')
                            ->build(),
                    ];
                }
                if (!$statement->expr instanceof Node\Expr\Assign) {
                    return [
                        RuleErrorBuilder::message('A method called set* must assign the value to a variable')
                            ->identifier('setter.noassignment')
                            ->build(),
                    ];
                }

                $inferredVariableName = lcfirst(substr($methodName, 3));
                $variableInExpression = $statement->expr->var;
                if (!$variableInExpression instanceof Node\Expr\PropertyFetch) {
                    return [
                        RuleErrorBuilder::message('A method called set* must assign to a local object variable')
                            ->identifier('setter.noobjectproperty')
                            ->build()
                    ];
                }
                if (!$variableInExpression->name instanceof Node\Identifier) {
                    // The left side is something like $this->foo->bar
                    // TODO support this
                    return [];
                }
                $actualVariableName = $variableInExpression->name->name;

                if ($inferredVariableName !== $actualVariableName) {
                    return [
                        RuleErrorBuilder::message(sprintf(
                            'Method %s() should assign to $%s, but assigns to $%s',
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
