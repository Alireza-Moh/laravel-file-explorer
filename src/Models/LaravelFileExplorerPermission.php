<?php

namespace AlirezaMoh\LaravelFileExplorer\Models;

use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LaravelFileExplorerPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(ConfigRepository::getUserModel());
    }
}
