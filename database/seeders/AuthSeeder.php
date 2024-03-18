<?php

namespace Database\Seeders;

use Database\Seeders\Auth\PermissionRoleSeeder;
use Database\Seeders\Auth\UserRoleSeeder;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class AuthSeeder.
 */
class AuthSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Reset cached roles and permissions
        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();
        

        // Truncate tables
        $tablesToTruncate = [
            Config::get('permission.table_names.model_has_permissions'),
            Config::get('permission.table_names.model_has_roles'),
            Config::get('permission.table_names.role_has_permissions'),
            Config::get('permission.table_names.permissions'),
            'users',
            'password_histories',
            'password_resets',
        ];

        foreach ($tablesToTruncate as $table) {
            $this->truncateTable($table);
        }

        // Seed data
        $this->call(UserSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(UserRoleSeeder::class);

        $this->enableForeignKeys();
    }

    /**
     * Truncate the specified table.
     *
     * @param  string  $table
     * @return void
     */
    protected function truncateTable(string $table)
    {
        try {
            \DB::table($table)->truncate();
        } catch (\Throwable $e) {
            $this->command->warn("Failed to truncate table '{$table}': {$e->getMessage()}");
        }
    }
}
