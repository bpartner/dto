<?php

namespace Bpartner\Dto;

use Bpartner\Dto\Contracts\DtoInterface;
use Bpartner\Dto\Exceptions\CreatorException;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

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
     * @param  class-string<T>  $classDTO
     * @param  array|null  $args
     * @param  int  $flag
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
        } catch (ReflectionException $e) {
            logger()->error($e->getMessage());
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
     * @throws \ReflectionException
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

            $propertyClassTypeName = $propertyClassType !== null ? $propertyClassType->getName() : '';

            app(Pipeline::class)
                ->send(
                    new CreatorValueData(
                        $propertyClassTypeName,
                        $instance,
                        $args,
                        $propertyClass,
                        $item,
                        $property,
                        $flag
                    )
                )
                ->through(TypeResolver::$resolvers)
                ->then(function ($result) {
                    return $result;
                });
        }

        return $instance;
    }

    /**
     * @param $flag
     * @param $name
     *
     * @return string
     */
    private static function transform($flag, $name): string
    {
        return match ($flag) {
            self::SNAKE_CASE => Str::snake($name),
            self::CAMEL_CASE => Str::camel($name),
            self::PASCAL_CASE => Str::studly($name),
            self::KEBAB_CASE => Str::kebab($name),
            self::UPPER_FIRST => Str::ucfirst($name),
            default => $name,
        };
    }
}
