<?php

declare(strict_types=1);

namespace App\Components\VerificationCode;

use App\Common\BaseService;
use App\Common\Contracts\DtoWithUser;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            VerificationCode::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return VerificationCode
     * @throws \Throwable
     */
    public function create(DtoWithUser $dto): Model
    {
        $timeout = (int)\config('verification.timeout', 60);
        $dto->expired_at = Carbon::now()->addSeconds($timeout);

        return parent::create($dto);
    }
}