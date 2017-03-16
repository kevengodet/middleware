<?php

namespace Adagio\Middleware;

final class Runner
{
    /**
     *
     * @var callable[]
     */
    private $middlewares = [];

    /**
     *
     * @param array $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        /* @var $middleware Middleware */
        $middleware = array_shift($this->middlewares);

        if (is_null($middleware)) {
            return end($args);
        }

        array_push($args, $this);

        return $middleware(...$args);
    }
}
