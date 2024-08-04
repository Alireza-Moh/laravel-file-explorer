<?php

namespace AlirezaMoh\LaravelFileExplorer\Models\Concerns;

use AlirezaMoh\LaravelFileExplorer\Models\LaravelFileExplorerPermission;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasLaravelFileExplorerPermission
{
    const CREATE_PERMISSION = 'create';

    const READ_PERMISSION = 'read';

    const WRITE_PERMISSION = 'write';

    const DELETE_PERMISSION = 'delete';

    const UPDATE_PERMISSION = 'update';

    const UPLOAD_PERMISSION = 'upload';

    const DOWNLOAD_PERMISSION = 'download';


    public function laravelFileExplorerPermissions(): HasOne
    {
        return $this->hasOne(LaravelFileExplorerPermission::class);
    }

    public function hasPermission(string $requestedPermission): bool
    {
        return in_array($requestedPermission, $this->laravelFileExplorerPermissions->permissions);
    }

    public function addPermissions(array $permissions = []): LaravelFileExplorerPermission
    {
        return $this->laravelFileExplorerPermissions()->create([
            'permissions' => $permissions ?? ConfigRepository::getPermissions()
        ]);
    }
}
