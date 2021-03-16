<?php
/**
 * File: GenreService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\Genre;

use App\Models\Course;
use App\Models\Person;
use App\Repository\GenreRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class GenreService
 * @package App\Services\Genre
 */
class GenreService
{
    private GenreRepository $repository;

    public function __construct(GenreRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Person $person
     * @return Collection|Course[]
     */
    public function getCoursesByPersonGenres(Person $person): Collection
    {
        $genres = $this->repository->getAllForPerson($person);
        return $this->repository->getCoursesByGenres($genres);
    }

    /**
     * @param Course $course
     * @return Collection|Person[]
     */
    public function getPersonsByCourseGenres(Course $course): Collection
    {
        $genres = $this->repository->getAllForCourse($course);
        return $this->repository->getPersonsByGenres($genres);
    }
}
