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
use App\Components\Loader;
use App\Events\Instructor\InstructorEvent;
use App\Events\Student\StudentEvent;
use App\Events\User\UserEvent;
use App\Http\Requests\Auth\DTO\RegisterUser;
use App\Models\Enum\InstructorStatus;
use App\Models\Enum\UserStatus;
use App\Models\Enum\UserType;
use App\Models\Person;
use App\Models\User;
use App\Services\UserRegister\Exceptions\UserAlreadyRegisteredWithSamePhoneNumber;
use App\Services\Verification\VerificationService;

class UserRegisterService extends BaseService
{
    protected Components\User\Facade $users;
    protected Components\Customer\Facade $customers;
    protected Components\Student\Facade $students;
    protected Components\Instructor\Facade $instructors;
    protected Components\Person\Facade $people;
    protected Components\VerificationCode\Facade $verificationCodes;
    protected VerificationService $verifyService;

    public function __construct(
        VerificationService $verifyService
    ) {
        $this->users = Loader::users();
        $this->customers = Loader::customers();
        $this->students = Loader::students();
        $this->instructors = Loader::instructors();
        $this->people = Loader::people();
        $this->verificationCodes = Loader::verificationCodes();
        $this->verifyService = $verifyService;
    }

    /**
     * You should call this method iteratively several times
     * until user is passed all verification steps
     * and finally registered
     *
     * @param RegisterUser $registerUser
     * @return User
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     * @throws Exceptions\PhoneIsNotVerifiedException
     * @throws \Exception
     * @throws \Throwable
     */
    public function registerUser(RegisterUser $registerUser): User
    {
        try {
            $verificationCode = $this->verificationCodes->findVerifiedById($registerUser->verification_code);
            $registerUser->phone = $verificationCode->phone_number;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new Exceptions\PhoneIsNotVerifiedException();
        }

        return \DB::transaction(function () use ($registerUser) {
            // Lookup for anybody with such phone number
            // Lookup for somebody with exact same name, gender and birthdate
            // Create new Person or update existing one
            $person = $this->createOrUpdatePerson($registerUser);

            // Create Instructor or Student
            match ($registerUser->user_type) {
                UserType::INSTRUCTOR => $this->createInstructorIfNotExist($person, $registerUser),
                UserType::STUDENT => $this->createStudentIfNotExist($person, $registerUser),
                UserType::USER => function () {},
                UserType::CUSTOMER => function () {},
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

            //\event(new UserRegisteredEvent($newUser));
            UserEvent::registered($newUser);

            return $newUser;
        });
    }

    /**
     * Get person if he has no user yet
     * otherwise throw exception
     *
     * @param RegisterUser $registerUser
     * @return Person|null
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     */
    protected function checkPeopleWithSamePhoneNumber(RegisterUser $registerUser): ?Person
    {
        $person = $this->people->getByPhoneNumber($registerUser->phone);

        if (null !== $person && null !== $person->user) {
            throw new UserAlreadyRegisteredWithSamePhoneNumber();
        }

        return $person;
    }

    /**
     * Check if user with such bio is already exists
     * otherwise throw error
     *
     * @param RegisterUser $registerUser
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     */
    protected function checkPeopleWithSameBio(RegisterUser $registerUser): void
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
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber|\Throwable
     */
    protected function createOrUpdatePerson(RegisterUser $registerUser): Person
    {
        /** @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber */
        $person = $this->checkPeopleWithSamePhoneNumber($registerUser);

        if (null === $person) {
            /** @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber */
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

        return null === $person
            ? $this->people->create($dto)
            : $this->people->findAndUpdate($person->id, $dto);
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @throws \Exception
     */
    protected function createStudentIfNotExist(Person $person, RegisterUser $registerUser): void
    {
        if (null !== $person->student) {
            return;
        }

        $dto = new Components\Student\Dto();
        $newStudent = $this->students->createFromPerson($dto, $person);

        // Fire event
        //\event(new StudentCreatedEvent($newStudent));
        StudentEvent::created($newStudent);

        $person->load('student');
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @return void
     * @throws \Exception
     */
    protected function createInstructorIfNotExist(Person $person, RegisterUser $registerUser): void
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
        //\event(new InstructorCreatedEvent($newInstructor));
        InstructorEvent::created($newInstructor);

        $person->load('instructor');
    }

    /**
     * @param Person $person
     * @param RegisterUser $registerUser
     * @return User
     * @throws \Exception
     */
    protected function createUser(Person $person, RegisterUser $registerUser): User
    {
        $dto = new Components\User\Dto();
        $dto->username = $registerUser->phone;
        $dto->password = $registerUser->password;
        $dto->status = UserStatus::PENDING;

        $newUser = $this->users->create($dto);

        // Fire event
        //\event(new UserCreatedEvent($newUser));
        UserEvent::created($newUser);

        return $newUser;
    }
}
