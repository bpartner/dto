<?php

namespace Bpartner\Dto\ConvertersValue;

use Bpartner\Dto\Contracts\HandledInterface;
use Carbon\Carbon;

class DatetimeParser implements HandledInterface
{
    public function handle($data, $next): mixed
    {
        if ($data instanceof Carbon) {
            return $this->transformCarbon($data);
        }

        return $next($data);
    }

    private function transformCarbon($value): string
    {
        return $value->format(config('dto.date-format'));
    }

}
