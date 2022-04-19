<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Models\Enum\CourseStatus;
use App\Models\Instructor;
use JetBrains\PhpStorm\ArrayShape;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;
    public string $name;
    public ?string $summary = null;
    public ?string $description = null;
    public bool $display;
    #[ArrayShape(['from' => "int", 'to' => "int"])] public array $age_restrictions;
    public ?\Illuminate\Http\UploadedFile $picture = null;
    public CourseStatus $status;
    public ?Instructor $instructor;
    public ?\Carbon\Carbon $starts_at = null;
    public ?\Carbon\Carbon $ends_at = null;
    public array $genres = [];
}