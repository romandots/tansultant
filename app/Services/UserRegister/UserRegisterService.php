<?php
/**
 * File: UserRegisterService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\UserRegister;

use App\Common\BaseService;
use App\Components;
use App\Components\User\Exceptions\UserAlreadyRegisteredWithSamePhoneNumber;
use App\Events\InstructorCreatedEvent;
use App\Events\StudentCreatedEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\DTO\RegisterUser;
use App\Models\Enum\InstructorStatus;
use App\Models\Enum\UserType;
use App\Models\Person;
use App\Models\User;
use App\Services\Verification\VerificationService;

class UserRegisterService extends BaseService
{
    protected Components\User\Facade $users;
    protected Components\Customer\Facade $customers;
    protected Components\Student\Facade $students;
    protected Components\Instructor\Facade $instructors;
    protected Components\Person\Facade $people;
    protected VerificationService $verifyService;

    public function __construct(
        Components\User\Facade $users,
        Components\Customer\Facade $customers,
        Components\Student\Facade $students,
        Components\Instructor\Facade $instructors,
        Components\Person\Facade $people,
        VerificationService $verifyService
    ) {
        $this->users = $users;
        $this->customers = $customers;
        $this->students = $students;
        $this->instructors = $instructors;
        $this->people = $people;
        $this->verifyService = $verifyService;
    }

    /**
     * @param RegisterUser $registerUser
     * @return Person|null
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     */
    private function checkPeopleWithSamePhoneNumber(RegisterUser $registerUser): ?Person
    {
        $person = $this->people->getByPhoneNumber($registerUser->phone);

        if (null !== $person && null !== $person->user) {
            throw new UserAlreadyRegisteredWithSamePhoneNumber();
        }

        return $person;
    }

    /**
     * @param RegisterUser $registerUser
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     */
    private function checkPeopleWithSameBio(RegisterUser $registerUser): void
    {
        $person = $this->people->getByNameGenderAndBirthDate(
            $registerUser->last_name,
            $registerUser->first_name,
            $registerUser->patronymic_name,
            $registerUser->gender,
            $registerUser->birth_date
        );

        if (null !== $person) {
            throw new Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber();
        }
    }

    /**
     * @param RegisterUser $registerUser
     * @return Person
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     * @throws \Exception
     */
    private function createOrUpdatePerson(RegisterUser $registerUser): Person
    {
        $person = $this->checkPeopleWithSamePhoneNumber($registerUser);

        if (null === $person) {
            $this->checkPeopleWithSameBio($registerUser);
        }

        $dto = new Components\Person\Dto();
        $dto->last_name = $registerUser->last_name;
        $dto->first_name = $registerUser->first_name;
        $dto->patronymic_name = $registerUser->patronymic_name;
        $dto->birth_date = $registerUser->birth_date;
        $dto->gender = $registerUser->gender;
        $dto->phone = $registerUser->phone;
        $dto->email = $registerUser->email;

        if (null === $person) {
            $person = $this->people->create($dto);
        } else {
            $this->people->findAndUpdate($person->id, $dto);
        }

        return $person;
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @throws \Exception
     */
    private function createStudentIfNotExist(Person $person, RegisterUser $registerUser): void
    {
        if (null !== $person->student) {
            return;
        }

        $dto = new Components\Student\Dto();
        $newStudent = $this->students->createFromPerson($dto, $person);

        // Fire event
        \event(new StudentCreatedEvent($newStudent));

        $person->load('student');
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @return void
     * @throws \Exception
     */
    private function createInstructorIfNotExist(Person $person, RegisterUser $registerUser): void
    {
        if (null !== $person->instructor) {
            return;
        }

        $dto = new Components\Instructor\Dto();
        $dto->name = "{$person->first_name} {$person->last_name}";
        $dto->description = $registerUser->description;
        $dto->display = false;
        $dto->status = InstructorStatus::FREELANCE;

        $newInstructor = $this->instructors->createFromPerson($dto, $person);

        // Fire event
        \event(new InstructorCreatedEvent($newInstructor));

        $person->load('instructor');
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @return User
     * @throws \Exception
     */
    private function createUser(Person $person, RegisterUser $registerUser): User
    {
        $dto = new Components\User\Dto();
        $dto->username = $registerUser->phone;
        $dto->password = $registerUser->password;

        $newUser = $this->users->createFromPerson($dto, $person);

        // Fire event
        \event(new UserCreatedEvent($newUser));

        return $newUser;
    }

    /**
     * You should call this method iteratively several times
     * until user is passed all verification steps
     * and finally registered
     *
     * @param RegisterUser $registerUser
     * @return User
     * @throws \App\Components\User\Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     * @throws \Exception
     */
    public function registerUser(RegisterUser $registerUser): User
    {
        return \DB::transaction(function () use ($registerUser) {
            // Lookup for anybody with such phone number
            // Lookup for somebody with exact same name, gender and birth date
            // Create new Person or update existing one
            $person = $this->createOrUpdatePerson($registerUser);

            // Create Instructor or Student
            match ($registerUser->user_type) {
                UserType::INSTRUCTOR => $this->createInstructorIfNotExist($person, $registerUser),
                UserType::STUDENT => $this->createStudentIfNotExist($person, $registerUser),
                UserType::USER => throw new \Exception('To be implemented'),
                UserType::CUSTOMER => throw new \Exception('To be implemented'),
            };

            // Create User
            $newUser = $this->createUser($person, $registerUser);

            // Auto approve new students
            if (UserType::STUDENT === $registerUser->user_type) {
                $this->users->approve($newUser);
            }

            // Remove verification codes
            $this->verifyService->cleanUp($person->phone);

            $newUser->load(['person.instructor', 'person.student', 'person.customer', 'person.user']);

            \event(new UserRegisteredEvent($newUser));

            return $newUser;
        });
    }
}
