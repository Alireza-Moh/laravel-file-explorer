<?php

namespace AlirezaMoh\LaravelFileExplorer\Models\Concerns;

use AlirezaMoh\LaravelFileExplorer\Models\LaravelFileExplorerPermission;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasLaravelFileExplorerPermission
{
    public function laravelFileExplorerPermissions(): HasOne
    {
        return $this->hasOne(LaravelFileExplorerPermission::class);
    }

    public function hasPermission($requestedPermission): bool
    {
        return in_array($requestedPermission, $this->laravelFileExplorerPermissions->permissions);
    }
}
