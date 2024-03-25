<?php

class TrivialSetterWithCorrectPropertyName
{
    private string $foo;

    public function setFoo(string $foo): void
    {
        $this->foo = $foo;
    }
}

class SimpleSetterWithoutAssign
{
    private string $foo;

    public function setFoo(string $foo): void
    {
        $this->foo;
    }
}

class SimpleSetterWithWrongVariableName
{
    private string $foo;

    private string $bar;

    public function setFoo(string $foo): void
    {
        $this->bar = $foo;
    }
}

class EmptySetter
{
    private string $foo;

    public function setFoo(string $foo): void
    {
    }
}
