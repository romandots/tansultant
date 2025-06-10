<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Models\Enum\CourseStatus;
use JetBrains\PhpStorm\ArrayShape;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public ?string $summary = null;
    public ?string $description = null;
    public bool $display;
    #[ArrayShape(['from' => "int", 'to' => "int"])] public array $age_restrictions;
    public ?\Illuminate\Http\UploadedFile $picture = null;
    public CourseStatus $status;
    public ?string $instructor_id = null;
    public ?string $formula_id = null;
    public ?\Carbon\Carbon $starts_at = null;
    public ?\Carbon\Carbon $ends_at = null;
    public array $genres = [];
}