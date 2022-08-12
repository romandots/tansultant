<?php

namespace App\Components\Person\Exceptions;

use App\Exceptions\SimpleValidationException;

class PersonAlreadyRegistered extends SimpleValidationException
{
    private string $name;
    private ?\Carbon\Carbon $birth_date;
    private \App\Models\Person $existing_record;
    private ?\App\Models\Enum\Gender $gender;

    public function __construct(\App\Components\Person\Dto $dto, \App\Models\Person $existingRecord)
    {
        $this->name = sprintf('%s %s %s', $dto->last_name, $dto->first_name, $dto->patronymic_name);
        $this->birth_date = $dto->birth_date;
        $this->gender = $dto->gender;
        $this->existing_record = $existingRecord;

        parent::__construct('last_name', 'unique');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Carbon\Carbon|null
     */
    public function getBirthDate(): ?\Carbon\Carbon
    {
        return $this->birth_date;
    }

    /**
     * @return \App\Models\Enum\Gender|null
     */
    public function getGender(): ?\App\Models\Enum\Gender
    {
        return $this->gender;
    }

    /**
     * @return \App\Models\Person
     */
    public function getExistingRecord(): \App\Models\Person
    {
        return $this->existing_record;
    }
}