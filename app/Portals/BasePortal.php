<?php

namespace App\Portals;

use App\Support\Modules\ModuleRegistry;
use Illuminate\Support\Collection;

abstract class BasePortal
{
    public function __construct(protected ModuleRegistry $moduleRegistry) {}

    abstract public function key(): string;

    public function modules(): Collection
    {
        return $this->moduleRegistry->forPortal($this->key());
    }
}
