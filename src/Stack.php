<?php

namespace Adagio\Middleware;

final class Stack
{
    /**
     *
     * @var callable[]
     */
    private $middlewares = [];

    /**
     *
     * @param callable[] $middlewares
     */
    public function __construct($middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    /**
     *
     * @param callable $middleware
     */
    public function push($middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     *
     * @param mixed $arg1
     * @param mixed $arg2
     * @param ...
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return (new Runner($this->middlewares))(...$args);
    }
}
