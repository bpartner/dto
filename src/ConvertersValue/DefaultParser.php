<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\HandledInterface;

class DefaultParser implements HandledInterface
{
    public function handle($data, $next): mixed
    {
        $res = $next($data);
        if (!$res) {
            return $data;
        }

        return $res;
    }
}
