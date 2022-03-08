<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\HandledInterface;

class MethodToArrayParser implements HandledInterface
{
    public function handle($data, $next): mixed
    {
        if (method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        return $next($data);
    }
}
