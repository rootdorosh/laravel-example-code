<?php
declare( strict_types = 1 );

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AbstractRepositoryInterface;
use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class AbstractRepository
 * @package App\Repositories\Eloquent
 */
class AbstractRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * AbstractRepository constructor.
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model::find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        return $this->model::findOrfail($id);
    }

    /**
     * @param $id      int
     * @param $request BaseFormRequest
     * @return mixed
     */
    public function firstOrFail(BaseFormRequest $request, int $id)
    {
        $where         = $request->getRouteFilterParams();
        $where[ 'id' ] = $id;

        return $this->model::where($where)
                           ->firstOrFail();
    }

    /**
     * @return mixed
     */
    public function paginate()
    {
        return $this->model::paginate();
    }

    /**
     * @param null $data
     * @return mixed
     */
    public function get($data = null)
    {
        return $data !== null
            ? $this->model::where($data)
                          ->get()
            : $this->model::get();
    }

    /**
     * @param array $options
     * @return Collection
     */
    public function all($params = []) : Collection
    {
        return $this->model::all();
    }

    /**
     * @param string $titleAttr
     * @param string $keyAttr
     * @return array
     */
    public function getList($titleAttr = 'title', $keyAttr = 'id') : array
    {
        return $this->model::all()->pluck($titleAttr, $keyAttr)->toArray();
    }

    /**
     *
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     *
     * @param array $params
     * @return array
     */
    public function getPaginateParams(array $params = []) : array
    {
        if (isset($params['per_page']) && $params['per_page'] == 0) {
            $params['per_page'] = 1000;
        }
        
        $page = isset($params['page']) ? $params['page'] : 1;
        $perPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $skip = ($page - 1) * $perPage;
        
        return [$perPage, $skip];
    }
}
