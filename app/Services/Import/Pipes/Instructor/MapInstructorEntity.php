<?php

namespace App\Services\Import\Pipes\Instructor;

use App\Components\Instructor\Dto;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapInstructorEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;
        $dto->name = $ctx->old->name;
        $dto->description = $ctx->old->description;
        $dto->status = match($ctx->old->status) {
            'exclusive', 'staff' => \App\Models\Enum\InstructorStatus::HIRED,
            'part-time' => \App\Models\Enum\InstructorStatus::FREELANCE,
            default => \App\Models\Enum\InstructorStatus::FIRED,
        };
        $dto->display = (bool)$ctx->old->show_on_site;

        return $next($ctx);
    }
}