<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\HandledInterface;

class ScalarParser implements HandledInterface
{
    public function handle($data, $next): mixed
    {
        if (!is_object($data) && !is_array($data)) {
            return $data;
        }

        return $next($data);
    }
}
