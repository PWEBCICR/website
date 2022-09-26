<?php

namespace App\Http\Requests;

use App\Models\Field;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreRefugeeRequest extends FormRequest
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
        $hiddens = new Field();
        $hiddens = $hiddens->getHidden();
        $fields = Field::where("status", ">", 0)->where('crew_id', Auth::user()->crew_id)->get();
        $fields = $fields->makeVisible($hiddens)->toArray();
        $rules = array_column($fields, 'validation_laravel', "id");

        return $rules;

    }
}
