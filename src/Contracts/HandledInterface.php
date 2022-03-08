<?php

namespace Bpartner\Dto\Contracts;

interface HandledInterface
{
    public function handle($data, $next): mixed;
}
