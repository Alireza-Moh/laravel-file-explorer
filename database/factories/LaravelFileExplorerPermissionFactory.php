<?php

namespace Database\Factories;

use AlirezaMoh\LaravelFileExplorer\Models\LaravelFileExplorerPermission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LaravelFileExplorerPermissionFactory extends Factory
{
    protected $model = LaravelFileExplorerPermission::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
        ];
    }
}
