<?php

namespace App\Http\Requests\Pm\Dialog;

trait Authorizable
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->dialog->members->contains($this->user()->id)) {
            return false;
        }

        return true;
    }
}
