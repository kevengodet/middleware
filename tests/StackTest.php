<?php

namespace Adagio\Tests\Middleware;

use Adagio\Middleware\Stack;

class StackTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $stack = new Stack([
                    function(\stdClass $obj, $next) {
                        $obj->value .= 'A';

                        return $next($obj);
                    }
                ]);

        $stack->push(function(\stdClass $obj, $next) {
                $obj->value .= 'B';

                return $next($obj);
        });

        $obj = new \stdClass;
        $obj->value = '>';

        $result = $stack($obj);

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals('>AB', $result->value);
    }
}
