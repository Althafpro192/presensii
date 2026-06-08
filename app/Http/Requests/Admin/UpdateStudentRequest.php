<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')->id;

        return [
            'nis' => 'required|string|unique:students,nis,' . $studentId,
            'name' => 'required|string|max:255',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'rfid' => 'nullable|string|unique:students,rfid,' . $studentId,
            'parent_phone' => 'nullable|string',
        ];
    }
}
