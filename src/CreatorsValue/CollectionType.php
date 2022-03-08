<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\HandledInterface;
use Bpartner\Dto\DtoFactory;
use Illuminate\Support\Collection;

class CollectionType implements HandledInterface
{
    /**
     * @param  \Bpartner\Dto\CreatorValueData  $data
     * @param $next
     *
     * @return bool
     * @throws \ReflectionException|\Bpartner\Dto\Exceptions\CreatorException
     */
    public function handle($data, $next): bool
    {
        if ($data->propertyClassTypeName === Collection::class) {
            $docType = $this->getClassFromPhpDoc($data->propertyClass->getDocComment());
            if ($docType) {
                $data->instance->{$data->item->name} = collect();
                foreach ($data->args[$data->property] as $el) {
                    /** @phpstan-ignore-next-line */
                    $data->instance->{$data->item->name}->push((new DtoFactory())->build($docType, $el));
                }

                return true;
            }
        }

        return false;
    }

    private function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
            preg_match('/(collection)<([a-zA-Z\d\\\]+)>/m', $phpDoc, $docType);

            return $docType[2] ?? null;
        }

        return null;
    }

}
