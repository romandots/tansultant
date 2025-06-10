<?php

declare(strict_types=1);

namespace App\Components\Genre;

use App\Common\BaseComponentService;
use App\Models\Course;
use App\Models\Genre;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Genre::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Person $person
     * @return Collection<Course>
     */
    public function getCoursesByPersonGenres(Person $person): Collection
    {
        $genres = $this->getRepository()->getAllForPerson($person);
        return $this->getRepository()->getCoursesByGenres($genres);
    }

    /**
     * @param Course $course
     * @return Collection<Person>
     */
    public function getPersonsByCourseGenres(Course $course): Collection
    {
        $genres = $this->getRepository()->getAllForCourse($course);
        return $this->getRepository()->getPersonsByGenres($genres);
    }
}