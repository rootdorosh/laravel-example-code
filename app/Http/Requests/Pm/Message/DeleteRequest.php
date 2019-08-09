<?php
declare( strict_types = 1 );

namespace App\Http\Requests\Pm\Message;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Pm\Dialog\Authorizable;

/**
 * Class DeleteRequest
 * @package App\Http\Requests\Pm\Message
 *
 */
class DeleteRequest extends BaseFormRequest
{
    use Authorizable {
		authorize as authorizable;
	}    
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->authorizable() && $this->recipient->user_id === $this->user()->id;
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
}
