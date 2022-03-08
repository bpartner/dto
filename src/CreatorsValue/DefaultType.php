<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\HandledInterface;

class DefaultType implements HandledInterface
{
    /**
     * @param  \Bpartner\Dto\CreatorValueData  $data
     * @param $next
     *
     * @return bool
     */
    public function handle($data, $next): bool
    {
        $result = $next($data);
        if (!$result) {
            $data->instance->{$data->item->name} = $data->args[$data->property] ?? null;
        }

        return true;
    }
}
