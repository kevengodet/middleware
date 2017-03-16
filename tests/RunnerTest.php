<?php

namespace Adagio\Tests\Middleware;

use Adagio\Middleware\Runner;

class RunnerTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $runner = new Runner([
            function(\stdClass $obj, $next) { $obj->value .= 'A'; return $next($obj); },
            function(\stdClass $obj, $next) { $obj->value .= 'B'; return $next($obj); },
        ]);

        $obj = new \stdClass;
        $obj->value = '>';

        $result = $runner($obj);

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals('>AB', $result->value);
    }
}
