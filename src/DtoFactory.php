<?php

namespace Bpartner\Dto;

use Bpartner\Dto\Contracts\DtoAbstract;
use Bpartner\Dto\Contracts\DtoInterface;
use Bpartner\Dto\Exceptions\CreatorException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class DtoFactory
{
    public const AS_IS       = 0;
    public const SNAKE_CASE  = 1;
    public const CAMEL_CASE  = 2;
    public const PASCAL_CASE = 3;
    public const KEBAB_CASE  = 4;
    public const UPPER_FIRST = 5;

    /**
     * @template T
     *
     * @param class-string<T> $classDTO
     * @param array|null      $args
     * @param int             $flag
     *
     * @return T
     * @throws CreatorException|ReflectionException
     *
     */
    public function build(string $classDTO, array $args = null, int $flag = self::AS_IS): DtoInterface
    {
        /** @var ReflectionClass | DtoInterface $instance */
        $instance = self::checkParameters($classDTO, $args);

        if ($instance instanceof DtoInterface) {
            return $instance;
        }

        $dto = new $classDTO();

        return self::create($dto, $args, $instance, $flag);
    }

    /**
     * @throws \Bpartner\Dto\Exceptions\CreatorException
     */
    private static function checkParameters($classDTO, $args): object
    {
        try {
            $refInstance = new ReflectionClass($classDTO);
        } catch (\ReflectionException $e) {
            throw new CreatorException("Class $classDTO not found.");
        }

        if ($args === null) {
            return new $classDTO();
        }

        if (method_exists($classDTO, 'withMap')) {
            return $classDTO::withMap($args);
        }

        return $refInstance;
    }

    /**
     * @throws \ReflectionException|\Bpartner\Dto\Exceptions\CreatorException
     */
    private static function create(
        DtoInterface $instance,
        array $args,
        ReflectionClass $refInstance,
        int $flag
    ): DtoInterface {
        foreach ($refInstance->getProperties() as $item) {
            $property = self::transform($flag, $item->name);
            $propertyClass = $refInstance->getProperty($item->name);
            $propertyClassType = $propertyClass->getType();

            $propertyClassTypeName = $propertyClassType !== null ? $propertyClassType->getName() : false;

            if (self::isScalarType($propertyClassTypeName, $instance, $args, $item, $property)) {
                continue;
            }

            if (self::isCarbonType($propertyClassTypeName, $instance, $args, $item, $property)) {
                continue;
            }

            if (self::isArrayType($propertyClassTypeName, $instance, $args, $propertyClass, $item, $property)) {
                continue;
            }

            if (self::isCollectionType($propertyClassTypeName, $instance, $args, $propertyClass, $item, $property)) {
                continue;
            }

            if (self::isDtoType($propertyClassTypeName, $instance, $args, $item, $property, $flag)) {
                continue;
            }

            $instance->{$item->name} = $args[$property] ?? null;
        }

        return $instance;
    }

    /**
     * @param string                               $type
     * @param \Bpartner\Dto\Contracts\DtoInterface $instance
     * @param array                                $args
     * @param                                      $current
     * @param                                      $property
     *
     * @return bool
     */
    private static function isScalarType(string $type, DtoInterface $instance, array $args, $current, $property): bool
    {
        if (in_array($type, ['int', 'float', 'string', 'bool'])) {
            $instance->{$current->name} = $args[$property] ?? null;

            return true;
        }

        return false;
    }

    /**
     * @param string                               $type
     * @param \Bpartner\Dto\Contracts\DtoInterface $instance
     * @param array                                $args
     * @param                                      $current
     * @param                                      $property
     *
     * @return bool
     */
    private static function isCarbonType(string $type, DtoInterface $instance, array $args, $current, $property): bool
    {
        if ($type === \Carbon\Carbon::class) {
            $instance->{$current->name} = $args[$property] ? Carbon::parse($args[$property]) : null;

            return true;
        }

        return false;
    }

    /**
     * @param string                               $type
     * @param \Bpartner\Dto\Contracts\DtoInterface $instance
     * @param array                                $args
     * @param \ReflectionProperty                  $propertyClass
     * @param                                      $current
     * @param                                      $property
     *
     * @return bool
     * @throws \Bpartner\Dto\Exceptions\CreatorException
     * @throws \ReflectionException
     */
    private static function isArrayType(
        string $type,
        DtoInterface $instance,
        array $args,
        ReflectionProperty $propertyClass,
        $current,
        $property
    ): bool {
        if ($type === 'array') {
            $docType = self::getClassFromPhpDoc($propertyClass->getDocComment());
            if ($docType) {
                foreach ($args[$property] as $el) {
                    /** @phpstan-ignore-next-line */
                    $instance->{$current->name}[] = self::build($docType, $el);
                }

                return true;
            }

            $instance->{$current->name} = $args[$property];

            return true;
        }

        return false;
    }

    /**
     * @param string                               $type
     * @param \Bpartner\Dto\Contracts\DtoInterface $instance
     * @param array                                $args
     * @param \ReflectionProperty                  $propertyClass
     * @param                                      $current
     * @param                                      $property
     *
     * @return bool
     * @throws \Bpartner\Dto\Exceptions\CreatorException
     * @throws \ReflectionException
     */
    private static function isCollectionType(
        string $type,
        DtoInterface $instance,
        array $args,
        ReflectionProperty $propertyClass,
        $current,
        $property
    ): bool {
        if ($type === \Illuminate\Support\Collection::class) {
            $docType = self::getClassFromPhpDoc($propertyClass->getDocComment());
            if ($docType) {
                $instance->{$current->name} = collect();
                foreach ($args[$property] as $el) {
                    /** @phpstan-ignore-next-line */
                    $instance->{$current->name}->push(self::build($docType, $el));
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @throws \ReflectionException
     * @throws \Bpartner\Dto\Exceptions\CreatorException
     */
    private static function isDtoType(
        string $type,
        DtoInterface $instance,
        array $args,
        $current,
        $property,
        $flag
    ): bool {
        if ($type && $instance instanceof DtoAbstract) {
            if (is_array(($args[$property] ?? null))) {
                /** @phpstan-ignore-next-line */
                $instance->{$current->name} = self::build($type, $args[$property], $flag);

                return true;
            }
            if (is_object($args[$property])) {
                $instance->{$current->name} = $args[$property];

                return true;
            }
            $instance->{$current->name} = self::build($type, $args, $flag);

            return true;
        }

        return false;
    }

    /**
     * @param string|false $phpDoc
     *
     * @return string|null
     */
    private static function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
            preg_match('/(array|collection)<([a-zA-Z\d\\\]+)>/m', $phpDoc, $docType);

            return $docType[2] ?? null;
        }

        return null;
    }

    /**
     * @param $flag
     * @param $name
     *
     * @return string
     */
    private static function transform($flag, $name): string
    {
        switch ($flag) {
            case self::SNAKE_CASE:
                $property = Str::snake($name);
                break;
            case self::CAMEL_CASE:
                $property = Str::camel($name);
                break;
            case self::PASCAL_CASE:
                $property = Str::studly($name);
                break;
            case self::KEBAB_CASE:
                $property = Str::kebab($name);
                break;
            case self::UPPER_FIRST:
                $property = Str::ucfirst($name);
                break;
            default :
                $property = $name;
        }

        return $property;
    }
}
