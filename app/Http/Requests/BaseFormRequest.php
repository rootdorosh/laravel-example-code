<?php

namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest as FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Exceptions\RouteParamValidationException;

/**
 * Class BaseFormRequest
 * @package App\Http\Requests
 */
abstract class BaseFormRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $locale = $this->header('locale');
        if (!empty($locale) && in_array($locale, config('translatable.locales'))) {
            app()->setLocale($locale);
        }
        
        return true;
    }

    /**
     * Get the failed validation response for the request.
     *
     * @param Validator $validator
     * @return HttpResponseException
     */
    protected function failedValidation(Validator $validator) : HttpResponseException
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('validation.the_given_data_was_invalid'),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get the failed validation route params.
     *
     * @param array $errors
     * @return void
     * @throw RouteParamValidationException
     */
    protected function failedValidationRouteParams(array $errors) : void
    {
        $validator = $this->getValidatorInstance();
        foreach ($errors as $field => $message) {
            $validator->errors()->add($field, $message);
        }
        
        throw (new RouteParamValidationException($validator));
    }
}
