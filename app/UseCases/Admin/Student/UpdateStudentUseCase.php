<?php

namespace App\UseCases\Admin\Student;

use App\Models\Student;

class UpdateStudentUseCase
{
    public function execute(Student $student, array $data): Student
    {
        $student->update($data);
        return $student;
    }
}
