<?php

namespace App\Domains\Auth\Http\Requests\Backend\Role;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Auth\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Class DeleteRoleRequest.
 */
class DeleteRoleRequest extends FormRequest
{
    protected $role;

    /**
     * Set the role to be checked for authorization.
     *
     * @param Role $role
     * @return $this
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if the role is not null and is not the Administrator role
        return $this->role && !$this->role->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('You cannot delete the Administrator role.'));
    }
}
