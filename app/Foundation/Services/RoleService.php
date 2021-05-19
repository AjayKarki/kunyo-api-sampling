<?php /** @noinspection SqlNoDataSourceInspection */

namespace Foundation\Services;

use Foundation\Models\Role;
use Illuminate\Support\Facades\DB;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;

/**
 * Class RoleService
 * @package Foundation\Services
 */
class RoleService extends BaseService
{

    /**
     * The Role instance
     *
     * @var $model
     */
    protected $model;

    /**
     * RoleService constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * Filter
     *
     * @param string|null $name
     * @return mixed
     */
    public function filter(string $name = null)
    {
        return $this->model->query()
            ->select('*')
            ->addSelect([
                'full_name' => app('db')
                    ->table('roles as parent')
                    ->selectRaw('CONCAT(name, " | ",roles.name) AS parent_name')
                    ->whereColumn('parent.id', 'roles.parent_id')
            ])
            ->where(function ($query) use ($name){
                if($name){
                    $query->where('name','like', '%'. $name .'%');
                }
            })
            ->latest()
            ->get();
    }

    /**
     * Get the list of Active roles
     *
     * @return mixed
     */
    public function getRoles()
    {
        return $this->model->where('status', Status::ACTIVE_STATUS)->pluck('name', 'id');
    }

    /**
     * Get name and Slug of Role
     *
     * @return mixed
     */
    public function getRolesWithSlug()
    {
        return $this->model->where('status', Status::ACTIVE_STATUS)->pluck('name', 'slug');
    }

    /**
     * Get the role by its slug
     *
     * @param $slug
     * @return mixed
     */
    public function getRoleBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getId($slug)
    {
        return $this->model->where('slug', $slug)->value('id') ?? 0;
    }

    /**
     * Return parent roles
     *
     * [ 'parent_id' === 0 ]
     *
     * @param $id
     * @return mixed
     */
    public function getParentRoles($id = null)
    {
        return $this->model->where([
            ['parent_id', 0],
            ['id', '!=', $id],
            ['status', Status::ACTIVE_STATUS],
        ])->get();
    }

    /**
     * Ret roles names from role id
     *
     * @param $roleId
     * @return mixed
     */
    public function getNameFromId($roleId)
    {
        return $this->model->whereIn('id', $roleId)->pluck('name', 'id');
    }
}
