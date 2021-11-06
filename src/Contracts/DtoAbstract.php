<?php

namespace Bpartner\Dto\Contracts;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class DtoAbstract implements DtoInterface, Arrayable
{
    public function toArray(): array
    {
        $result = [];
        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $result[$key] = $value;
                continue;
            }

            if ($value instanceof Carbon) {
                $result[$key] = $this->transformCarbon($value);
                continue;
            }

            if ($value instanceof Collection) {
                $result[$key] = $this->transformCollection($value);
            }

            if (is_array($value)) {
                $result[$key] = $this->transformArray($value);
                continue;
            }

            if (method_exists($value, 'toArray')) {
                $result[$key] = $value->toArray();
                continue;
            }

            $result[$key] = $value;
        }

        return json_decode(json_encode($result), true);
    }

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
    private function transformToFlatArray(array $array): array
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

    private function containsArray($array): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                return true;
            }
        }

        return false;
    }

    private function transformCarbon($value): string
    {
        return $value->format(config('dto.date-format'));
    }

    private function transformCollection($value): array
    {
        return $value->map(fn(DtoInterface $item) => $item->toArray())
                     ->toArray();
    }

    private function transformArray(array $value): array
    {
        return collect($value)
            ->map(function ($item) {
                if ($item instanceof DtoInterface) {
                    return $item->toArray();
                }
                return $item;
            })
            ->toArray();
    }

    /**
     * @param array $data
     *
     * @return $this
     *
     * public static function withMap(array $data): self
     * {
     *       $instance = new static;
     *      //Data manipulations
     *      return $instance;
     * }
     */
}
