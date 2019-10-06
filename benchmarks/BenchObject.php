<?php

namespace Opportus\ObjectMapper\Benchmarks;

class BenchObject
{
    private $a;
    private $b;

    public function __construct(int $a)
    {
        $this->a = $a;
    }

    public function getA(): int
    {
        return $this->a;
    }

    public function getB(): int
    {
        return $this->b;
    }

    public function setB(int $b)
    {
        $this->b = $b;
    }
}
