<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\DtoInterface;
use Bpartner\Dto\Contracts\HandledInterface;

class ArrayParser implements HandledInterface
{

    public function handle($data, $next): mixed
    {
        if (is_array($data)) {
            return $this->transformArray($data);
        }

        return $next($data);
    }

    private function transformArray(array $value): array
    {
        return collect($value)
            ->map(function ($item) {
                if ($item instanceof DtoInterface) {
                    return $item->toArray();
                }
                return $item;
            })
            ->toArray();
    }

}
