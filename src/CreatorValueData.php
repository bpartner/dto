<?php

namespace Bpartner\Dto;

use Bpartner\Dto\Contracts\DtoInterface;

class CreatorValueData
{
    public function __construct(
        public string $propertyClassTypeName,
        public DtoInterface $instance,
        public array $args,
        public $propertyClass,
        public $item,
        public string $property,
        public int $flag
    ) {
    }
}
