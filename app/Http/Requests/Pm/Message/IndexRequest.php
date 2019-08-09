<?php
declare( strict_types = 1 );

namespace App\Http\Requests\Pm\Message;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Pm\Dialog\Authorizable;

/**
 * Class IndexRequest
 * @package App\Http\Requests\Event
 *
 * @bodyParam per_page int per page
 * @bodyParam page int page
 * @bodyParam q string text for search
 */
class IndexRequest extends BaseFormRequest
{
    use Authorizable;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'q' => 'nullable|string',
        ];
    }
}
