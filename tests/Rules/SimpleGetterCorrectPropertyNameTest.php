<?php

declare(strict_types=1);

namespace AndreasWolf\PhpstanTrivialAccessors\Tests\Rules;

use AndreasWolf\PhpstanTrivialAccessors\Rules\SimpleGetterCorrectPropertyName;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<SimpleGetterCorrectPropertyName>
 */
class SimpleGetterCorrectPropertyNameTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new SimpleGetterCorrectPropertyName();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/simple-getters.php'], [
            [
                'A method called get* must not be empty, but return a value',
                15,
            ],
            [
                'A method called get* should return a local object variable',
                22,
            ],
            [
                'Method getFoo() should return $foo, but returns $bar',
                34,
            ],
        ]);
    }
}
