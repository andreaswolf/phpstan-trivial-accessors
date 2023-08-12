<?php

class TrivialGetterWithCorrectPropertyName
{
    private string $foo;

    public function getFoo(): string
    {
        return $this->foo;
    }
}

class EmptyGetter
{
    public function getFoo()
    {
    }
}

class GetterReturningConstantValue
{
    public function getFoo()
    {
        return 'foo';
    }
}

class GetterReturningWrongClassVariable
{
    private string $foo;

    private string $bar;

    public function getFoo()
    {
        return $this->bar;
    }
}
