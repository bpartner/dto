<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\DtoAbstract;
use Bpartner\Dto\Contracts\HandledInterface;
use Bpartner\Dto\DtoFactory;

class DtoType implements HandledInterface
{
    /**
     * @param  \Bpartner\Dto\CreatorValueData  $data
     * @param $next
     *
     * @return bool
     * @throws \Bpartner\Dto\Exceptions\CreatorException
     * @throws \ReflectionException
     */
    public function handle($data, $next): bool
    {
        if ($data->propertyClassTypeName && $data->instance instanceof DtoAbstract) {
            if (is_array(($data->args[$data->property] ?? null))) {
                /** @phpstan-ignore-next-line */
                $data->instance->{$data->item->name} = (new DtoFactory())->build(
                    $data->propertyClassTypeName,
                    $data->args[$data->property],
                    $data->flag
                );

                return true;
            }

            if (is_object($data->args[$data->property])) {
                $data->instance->{$data->item->name} = $data->args[$data->property];

                return true;
            }

            $data->instance->{$data->item->name} = (new DtoFactory())->build(
                $data->propertyClassTypeName,
                $data->args[$data->property],
                $data->flag
            );

            return true;
        }

        return $next($data);
    }
}
