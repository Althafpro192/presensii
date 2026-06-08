<?php

namespace App\UseCases\Admin\Student;

use App\Models\Student;
use App\Models\User;

class CreateStudentUseCase
{
    public function execute(array $data, User $user): Student
    {
        $data['school_id'] = $user->school_id;
        return Student::create($data);
    }
}
