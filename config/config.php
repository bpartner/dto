<?php

/*
 * You can place your custom package configuration in here.
 */

use Bpartner\Dto\ConvertersValue\ArrayParser;
use Bpartner\Dto\ConvertersValue\CollectionParser;
use Bpartner\Dto\ConvertersValue\DatetimeParser;
use Bpartner\Dto\ConvertersValue\DefaultParser;
use Bpartner\Dto\ConvertersValue\MethodToArrayParser;
use Bpartner\Dto\ConvertersValue\ScalarParser;

return [
    'date-format' => 'd-m-Y',
    'parsers' => [
        DefaultParser::class,
        ScalarParser::class,
        DatetimeParser::class,
        CollectionParser::class,
        ArrayParser::class,
        MethodToArrayParser::class
    ],
];
