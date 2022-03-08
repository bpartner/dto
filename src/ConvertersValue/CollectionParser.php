<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\DtoInterface;
use Bpartner\Dto\Contracts\HandledInterface;
use Illuminate\Support\Collection;

class CollectionParser implements HandledInterface
{
    public function handle($data, $next): mixed
    {
        if ($data instanceof Collection) {
            return $this->transformCollection($data);
        }

        return $next($data);
    }

    private function transformCollection($value): array
    {
        return $value->map(
        /**
         * @throws \JsonException
         */ fn(DtoInterface $item) => $item->toArray())
                     ->toArray();
    }

}
