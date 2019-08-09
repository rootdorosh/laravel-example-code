<?php
declare( strict_types = 1 );

namespace App\Http\Requests\Pm\Dialog;

use App\Http\Requests\BaseFormRequest;

/**
 * Class DeleteRequest
 * @package App\Http\Requests\Pm\Dialog
 *
 */
class DeleteRequest extends BaseFormRequest
{
    use Authorizable;    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
