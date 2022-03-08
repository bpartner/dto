<?php

namespace Bpartner\Dto;

class TypeResolver
{
    public static array $resolvers = [];

    public static function register($name)
    {
        return self::$resolvers[] = $name;
    }
}
