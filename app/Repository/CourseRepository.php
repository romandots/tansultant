<?php
/**
 * File: CourseRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StoreCourse as CourseDto;
use App\Models\Course;

/**
 * Class CourseRepository
 * @package App\Repository
 */
class CourseRepository
{
    /**
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Model|Course
     */
    public function find(string $id): Course
    {
        $course = Course::query()->findOrFail($id);
        $course->load('instructor');

        return $course;
    }

    /**
     * @param CourseDto $dto
     * @return Course
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function create(CourseDto $dto): Course
    {
        $course = new Course;
        $course->id = \uuid();
        $this->fill($course, $dto);
        $course->save();

        return $course;
    }

    /**
     * @param Course $course
     * @param CourseDto $dto
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(Course $course, CourseDto $dto): void
    {
        $this->fill($course, $dto);
        $course->save();
    }

    /**
     * @param Course $course
     * @param \Illuminate\Http\UploadedFile $file
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function savePicture(Course $course, \Illuminate\Http\UploadedFile $file): void
    {
        $name = \Hash::make($file->get());
        $path = $this->getPicturePath($name);
        $course->picture = $file->storePubliclyAs($path, $name);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPicturePath(string $name): string
    {
        $path = \config('uploads.paths.course_pictures', 'uploads/course_pictures');

        return "{$path}/{$name[0]}/{$name}";
    }

    /**
     * @param Course $course
     * @param CourseDto $dto
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function fill(Course $course, CourseDto $dto): void
    {
        $course->name = $dto->name;
        $course->status = $dto->status;
        $course->summary = $dto->summary;
        $course->description = $dto->description;
        if (null !== $dto->picture) {
            $this->savePicture($course, $dto->picture);
        }
        $course->age_restrictions = $dto->age_restrictions;
        $course->instructor_id = $dto->instructor_id;
        $course->starts_at = $dto->starts_at;
        $course->ends_at = $dto->ends_at;
    }

    /**
     * @param Course $course
     * @throws \Exception
     * @todo delete all future lessons of the course
     */
    public function delete(Course $course): void
    {
        $course->delete();
    }
}
