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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Class CourseRepository
 * @package App\Repository
 */
class CourseRepository
{
    /**
     * @return Collection|Course[]
     */
    public function getAll(): Collection
    {
        return Course::query()
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Model|Course
     */
    public function find(string $id): Course
    {
        $course = Course::query()
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->firstOrFail();
        $course->load('instructor');

        return $course;
    }

    public function findDeleted(string $id): Course
    {
        $course = Course::query()
            ->where('id', $id)
            ->whereNotNull('deleted_at')
            ->firstOrFail();
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
        $course->created_at = Carbon::now();
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
        $course->updated_at = Carbon::now();
        $course->save();
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|false
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function savePicture(\Illuminate\Http\UploadedFile $file)
    {
        $name = Hash::make($file->get());
        $path = $this->getPicturePath($name);
        return $file->storePubliclyAs($path, $name);
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
        $course->display = $dto->display;
        $course->summary = $dto->summary;
        $course->description = $dto->description;
        if (null !== $dto->picture) {
            $picture = $this->savePicture($dto->picture);
            $course->picture = $picture ?? null;
        }
        $course->age_restrictions = [
            'from' => $dto->age_restrictions['from'] ?? null,
            'to' => $dto->age_restrictions['to'] ?? null,
        ];
        $course->instructor_id = $dto->instructor->id;
        $course->starts_at = $dto->starts_at;
        $course->ends_at = $dto->ends_at;
    }

    /**
     * @param Course $course
     */
    public function delete(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->deleted_at = Carbon::now();
        $course->save();
    }
    /**
     * @param Course $course
     */
    public function restore(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->deleted_at = null;
        $course->save();
    }

    /**
     * @param Course $course
     */
    public function disable(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->status = Course::STATUS_DISABLED;
        $course->save();
    }

    /**
     * @param Course $course
     */
    public function enable(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->status = Course::STATUS_ACTIVE;
        $course->save();
    }
}
