<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Models\Course;
use App\Models\Enum\CourseStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Course make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Course> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Course find(string $id)
 * @method Course findTrashed(string $id)
 * @method Course create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Course $record)
 * @method void restore(Course $record)
 * @method void forceDelete(Course $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseRepository
{
    public function __construct() {
        parent::__construct(
            Course::class,
            ['name']
        );
    }

    /**
     * @param Course $record
     * @param Dto $dto
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->status = $dto->status;
        $record->display = $dto->display;
        $record->summary = $dto->summary;
        $record->description = $dto->description;
        if (null !== $dto->picture) {
            $picture = $this->savePicture($dto->picture);
            $record->picture = $picture ?? null;
        }
        $record->age_restrictions = [
            'from' => $dto->age_restrictions['from'] ?? null,
            'to' => $dto->age_restrictions['to'] ?? null,
        ];
        $record->instructor_id = $dto->instructor?->id;
        $record->starts_at = $dto->starts_at;
        $record->ends_at = $dto->ends_at;
    }

    public function setPending(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->status = CourseStatus::PENDING;
        $course->save();
    }

    public function setActive(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->status = CourseStatus::ACTIVE;
        $course->save();
    }

    public function setDisabled(Course $course): void
    {
        $course->updated_at = Carbon::now();
        $course->status = CourseStatus::DISABLED;
        $course->save();
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|false
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function savePicture(\Illuminate\Http\UploadedFile $file): bool|string
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
}