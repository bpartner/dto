<?php

namespace Bpartner\Dto\CreatorsValue;

use Bpartner\Dto\Contracts\HandledInterface;
use Bpartner\Dto\DtoFactory;

class ArrayType implements HandledInterface
{
    /**
     * @param  \Bpartner\Dto\CreatorValueData  $data
     * @param $next
     *
     * @return bool
     * @throws \Bpartner\Dto\Exceptions\CreatorException|\ReflectionException
     */
    public function handle($data, $next): bool
    {
        if ($data->propertyClassTypeName === 'array') {
            $docType = $this->getClassFromPhpDoc($data->propertyClass->getDocComment());
            if ($docType) {
                foreach ($data->args[$data->property] as $el) {
                    /** @phpstan-ignore-next-line */
                    $data->instance->{$data->item->name}[] = (new DtoFactory())->build($docType, $el);
                }

                return true;
            }

            $data->instance->{$data->item->name} = $data->args[$data->property];

            return true;
        }

        return false;
    }

    private function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
            preg_match('/(array)<([a-zA-Z\d\\\]+)>/m', $phpDoc, $docType);

            return $docType[2] ?? null;
        }

        return null;
    }
}
