<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\Traits\Attribute\RoleAttribute;
use App\Domains\Auth\Models\Traits\Method\RoleMethod;
use App\Domains\Auth\Models\Traits\Scope\RoleScope;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Auth\Models\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class Role.
 */
class Role extends SpatieRole
{
    public static function create(array $attributes = [])
{
    $attributes['guard_name'] = $attributes['guard_name'] ?? Auth::getDefaultDriver();


    if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->exists()) {
        // Log a message or handle it differently
        Log::info("Role '{$attributes['name']}' already exists for guard '{$attributes['guard_name']}'");
        
        // Return null to indicate that the role already exists
        return null;
    }

    return static::query()->create($attributes);
}

    use HasFactory,
        RoleAttribute,
        RoleMethod,
        RoleScope;

    /**
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    /**
     * @var string[]
     *
     */
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RoleFactory::new();
    }
}
