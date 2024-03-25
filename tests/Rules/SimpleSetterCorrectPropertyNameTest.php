<?php

declare(strict_types=1);

namespace AndreasWolf\PhpstanTrivialAccessors\Tests\Rules;

use AndreasWolf\PhpstanTrivialAccessors\Rules\SimpleSetterCorrectPropertyName;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<SimpleSetterCorrectPropertyName>
 */
class SimpleSetterCorrectPropertyNameTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new SimpleSetterCorrectPropertyName();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/simple-setters.php'], [
            [
                'A method called set* must assign the value to a variable',
                17,
            ],
            [
                'Method setFoo() should assign to $foo, but assigns to $bar',
                29,
            ],
            [
                'A method called set* must not be empty, but assign the passed value to a variable',
                39,
            ]
        ]);
    }
}
