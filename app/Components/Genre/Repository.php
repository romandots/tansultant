<?php

declare(strict_types=1);

namespace App\Components\Genre;

use App\Models\Course;
use App\Models\Genre;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Genre make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Genre> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Genre find(string $id)
 * @method Genre findTrashed(string $id)
 * @method Genre create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Genre $record)
 * @method void restore(Genre $record)
 * @method void forceDelete(Genre $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Genre::class,
            ['name']
        );
    }

    public function createWithName(string $name): Tag
    {
        return Tag::findOrCreate($name, Genre::class);
    }

    public function rename(string $oldName, string $newName): void
    {
        $tag = Tag::findFromString($oldName, Genre::class);
        $tag->name = $newName;
        $tag->save();
    }

    /**
     * @param Genre $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
    }

    /**
     * @param Person $person
     * @return string[]
     */
    public function getAllForPerson(Person $person): array
    {
        return $person
            ->tagsWithType(Genre::class)
            ->pluck('name')
            ->all();
    }

    /**
     * @param Course $course
     * @return string[]
     */
    public function getAllForCourse(Course $course): array
    {
        return $course
            ->tagsWithType(Genre::class)
            ->pluck('name')
            ->all();
    }

    /**
     * @param string[] $genres
     * @return \Illuminate\Support\Collection|Course[]
     */
    public function getCoursesByGenres(array $genres): \Illuminate\Support\Collection
    {
        return Course::withAnyTags($genres, Genre::class)->get();
    }

    /**
     * @param string[] $genres
     * @return \Illuminate\Support\Collection|Person[]
     */
    public function getPersonsByGenres(array $genres): \Illuminate\Support\Collection
    {
        return Person::withAnyTags($genres, Genre::class)->get();
    }
}