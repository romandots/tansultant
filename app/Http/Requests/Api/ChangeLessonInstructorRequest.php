<?php
/**
 * File: ChangeLessonInstructorRequest.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Instructor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ChangeLessonInstructorRequest
 * @package App\Http\Requests\Api
 * @property-read string $instructor_id
 */
class ChangeLessonInstructorRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'instructor_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ]
        ];
    }
}
