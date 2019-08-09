<?php
declare( strict_types = 1 );

namespace App\Http\Requests\Pm\Message;

use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\Pm\Dialog\Authorizable;

/**
 * Class SendRequest
 * @package App\Http\Requests\Pm\Message
 * @bodyParam text string required message text
 */
class SendRequest extends BaseFormRequest
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
            'text' => 'required|string|max:1024',
        ];
    }
}
