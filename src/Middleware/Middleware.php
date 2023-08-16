<?php

namespace Nebula\Middleware;

use Nebula\Interfaces\Http\Request;
use Nebula\Interfaces\Middleware\Middleware as NebulaMiddleware;
use Closure;

/**
 * Adpated from Optimus\Onion
 */
class Middleware
{
    public function __construct(private array $layers = [])
    {
    }

    public function layer($layers): Middleware
    {
        if ($layers instanceof Middleware) {
            $layers = $layers->toArray();
        }

        if ($layers instanceof NebulaMiddleware) {
            $layers = [$layers];
        }

        if (!is_array($layers)) {
            throw new \InvalidArgumentException(
                get_class($layers) . " is not Nebula middlware."
            );
        }

        return new static(array_merge($this->layers, $layers));
    }

    public function handle(Request $request, Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);

        $layers = array_reverse($this->layers);

        $next = array_reduce(
            $layers,
            function ($nextLayer, $layer) {
                return $this->createLayer($nextLayer, $layer);
            },
            $coreFunction
        );

        return $next($request);
    }

    public function toArray(): array
    {
        return $this->layers;
    }

    private function createCoreFunction(Closure $core): Closure
    {
        return function ($object) use ($core) {
            return $core($object);
        };
    }

    private function createLayer($nextLayer, $layer): Closure
    {
        return function ($object) use ($nextLayer, $layer) {
            return $layer->handle($object, $nextLayer);
        };
    }
}
