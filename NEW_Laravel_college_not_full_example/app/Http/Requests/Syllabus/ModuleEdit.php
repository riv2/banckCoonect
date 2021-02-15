<?php

namespace App\Http\Requests\Syllabus;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ModuleEdit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRight('themes','edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'module_id'     => 'required|exists:syllabus_modules,id',
            'language'      => 'required|in:ru,en,kz',
            'discipline_id' => 'required|exists:disciplines,id'
        ];
    }

    /**
     * Inject GET parameter "type" into validation data
     *
     * @param array $keys Properties to only return
     *
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['discipline_id'] = $this->route('disciplineId');
        $data['module_id'] = $this->route('module_id');

        return $data;
    }
}
