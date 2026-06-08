<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handle authorization via middleware or policies
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'rfid' => 'nullable|string|unique:students,rfid',
            'parent_phone' => 'nullable|string',
        ];
    }
}
