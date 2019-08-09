<?php
declare( strict_types = 1 );

namespace App\Http\Requests\Pm\Dialog;

use App\Http\Requests\BaseFormRequest;

/**
 * Class IdRequest
 * @package App\Http\Requests\Pm\Dialog
 *
 * @bodyParam users array required ids of users, members of dialog
 */
class IdRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users.*' => 'required|distinct|exists:users,id',
        ];
    }
}
