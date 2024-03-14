<?php

namespace Database\Seeders;

use Database\Seeders\Auth\PermissionRoleSeeder;
use Database\Seeders\Auth\UserRoleSeeder;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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

        // Truncate tables with foreign key constraints
        $this->truncateTables([
            Config::get('permission.table_names.model_has_permissions'),
            Config::get('permission.table_names.model_has_roles'),
            Config::get('permission.table_names.role_has_permissions'),
            Config::get('permission.table_names.permissions'),
            'users',
            'password_histories',
            'password_resets',
        ]);

        $this->call(UserSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(UserRoleSeeder::class);

        $this->enableForeignKeys();
    }

    /**
     * Truncate tables.
     *
     * @param array $tables
     */
    protected function truncateTables(array $tables)
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
