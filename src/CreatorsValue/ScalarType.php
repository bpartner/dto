<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\HandledInterface;

class ScalarType implements HandledInterface
{
    /**
     * @param  \Bpartner\Dto\CreatorValueData  $data
     * @param $next
     *
     * @return bool
     */
    public function handle($data, $next): bool
    {
        if (($data->args[$data->property] ?? null) && in_array($data->propertyClassTypeName, [
                'int',
                'float',
                'string',
                'bool',
            ])) {
            $data->instance->{$data->item->name} = $data->args[$data->property];

            return true;
        }

        return $next($data);
    }
}
