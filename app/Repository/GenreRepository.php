<?php
/**
 * File: GenreRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Course;
use App\Models\Genre;
use App\Models\Person;
use Spatie\Tags\Tag;

class GenreRepository
{
    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return Tag::query()
            ->where('type', Genre::class)
            ->pluck('name')
            ->all();
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

    public function create(string $name): Tag
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
     * @param string $genre
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Tag
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $genre): Tag
    {
        $locale = \app()->getLocale();
        return Tag::query()
            ->where("name->{$locale}", $genre)
            ->where('type', Genre::class)
            ->firstOrFail();
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
