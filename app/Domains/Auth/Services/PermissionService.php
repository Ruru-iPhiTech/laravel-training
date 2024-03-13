<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\Permission; // Add this line to import Permission model
use App\Services\BaseService;

/**
 * Class PermissionService.
 */
class PermissionService extends BaseService // Corrected class name to remove the extra 's' in 'PermissionServices'
{
    /**
     * PermissionService constructor.
     *
     * @param  Permission  $permission
     */
    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    /**
     * Get categorized permissions.
     *
     * @return mixed
     */
    public function getCategorizedPermissions()
    {
        return $this->model::isMaster()
            ->with('children')
            ->get();
    }

    /**
     * Get uncategorized permissions.
     *
     * @return mixed
     */
    public function getUncategorizedPermissions()
    {
        return $this->model::singular()
            ->orderBy('sort', 'asc')
            ->get();
    }
}
