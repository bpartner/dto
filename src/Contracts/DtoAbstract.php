<?php

namespace Bpartner\Dto\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Pipeline;
use JetBrains\PhpStorm\Pure;

abstract class DtoAbstract implements DtoInterface, Arrayable
{
    protected const CLASS_FORM_REQUEST = '';

    public function __construct(array $data = [])
    {
        if (!$data && static::CLASS_FORM_REQUEST) {
            $data = app(static::CLASS_FORM_REQUEST)->all();
        }
        $this->createFromArray($data);
    }

    /**
     * @throws \JsonException
     */
    public function toArray(): array
    {
        $result = [];
        $properties = get_object_vars($this);
        $parsers = config('dto.parsers');
        foreach ($properties as $key => $value) {
            $result[$key] = app(Pipeline::class)
                ->send($value)
                ->through($parsers)
                ->then(function ($result) {
                    return $result;
                });
        }

        return json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function flatArray(): array
    {
        $array = $this->toArray();

        return $this->transformToFlatArray($array);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    #[Pure]
    protected function transformToFlatArray(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($this->containsArray($value)) {
                    $result[$key] = $value;
                    continue;
                }

                foreach ($value as $subKey => $subValue) {
                    $result[$subKey] = $subValue;
                }
                continue;
            }
            $result[$key] = $value;
        }

        return $result;
    }

    protected function containsArray($array): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                return true;
            }
        }

        return false;
    }

    protected function createFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Creat DTO with custom mapping
     *
     * @param  array  $data
     *
     * @return $this
     */
//    public static function withMap(array $data): DtoInterface
//    {
//        return new static;
//    }
}
