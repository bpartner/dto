<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\HandledInterface;
use Illuminate\Support\Carbon;

class CarbonType implements HandledInterface
{
    /**
     * @param \Bpartner\Dto\CreatorValueData $data
     * @param $next
     *
     * @return bool
     */
    public function handle($data, $next): bool
    {
        if ($data->propertyClassTypeName === \Carbon\Carbon::class) {
            $data->instance->{$data->item->name} = $data->args[$data->property]
                ? Carbon::parse($data->args[$data->property])
                : null;

            return true;
        }

        return false;

    }
}
