<?php
/**
 * File: UserRegisterService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\UserRegister;

use App\Events\InstructorCreatedEvent;
use App\Events\StudentCreatedEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\DTO\RegisterUser;
use App\Http\Requests\DTO\StoreInstructor;
use App\Http\Requests\DTO\StorePerson;
use App\Http\Requests\DTO\StoreStudent;
use App\Http\Requests\ManagerApi\DTO\StoreUser;
use App\Models\Instructor;
use App\Models\Person;
use App\Models\User;
use App\Repository\CustomerRepository;
use App\Repository\InstructorRepository;
use App\Repository\PersonRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Services\Verify\VerificationService;

class UserRegisterService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var StudentRepository
     */
    private StudentRepository $studentRepository;

    /**
     * @var InstructorRepository
     */
    private InstructorRepository $instructorRepository;

    /**
     * @var PersonRepository
     */
    private PersonRepository $personRepository;

    /**
     * @var VerificationService
     */
    private VerificationService $verifyService;

    /**
     * UserRegisterService constructor.
     * @param UserRepository $userRepository
     * @param CustomerRepository $customerRepository
     * @param StudentRepository $studentRepository
     * @param InstructorRepository $instructorRepository
     * @param PersonRepository $personRepository
     * @param VerificationService $verifyService
     */
    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository,
        StudentRepository $studentRepository,
        InstructorRepository $instructorRepository,
        PersonRepository $personRepository,
        VerificationService $verifyService
    ) {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->studentRepository = $studentRepository;
        $this->instructorRepository = $instructorRepository;
        $this->personRepository = $personRepository;
        $this->verifyService = $verifyService;
    }

    /**
     * @param RegisterUser $registerUser
     * @return Person|null
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
     */
    private function checkPeopleWithSamePhoneNumber(RegisterUser $registerUser): ?Person
    {
        $person = $this->personRepository->getByPhoneNumber($registerUser->phone);

        if (null !== $person && null !== $person->user) {
            throw new Exceptions\UserAlreadyRegisteredWithSamePhoneNumber();
        }

        return $person;
    }

    /**
     * @param RegisterUser $registerUser
     * @throws Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber
     */
    private function checkPeopleWithSameBio(RegisterUser $registerUser): void
    {
        $person = $this->personRepository->getByNameGenderAndBirthDate(
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

        $storePerson = new StorePerson();
        $storePerson->last_name = $registerUser->last_name;
        $storePerson->first_name = $registerUser->first_name;
        $storePerson->patronymic_name = $registerUser->patronymic_name;
        $storePerson->birth_date = $registerUser->birth_date;
        $storePerson->gender = $registerUser->gender;
        $storePerson->phone = $registerUser->phone;
        $storePerson->email = $registerUser->email;

        if (null === $person) {
            $person = $this->personRepository->createFromDto($storePerson);
        } else {
            $this->personRepository->update($person, $storePerson);
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

        $newStudent = $this->studentRepository->createFromPerson($person, new StoreStudent());

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

        $storeInstructor = new StoreInstructor();
        $storeInstructor->name = "{$person->first_name} {$person->last_name}";
        $storeInstructor->description = $registerUser->description;
        $storeInstructor->display = false;
        $storeInstructor->status = Instructor::STATUS_FREELANCE;

        $newInstructor = $this->instructorRepository->createFromPerson($person, $storeInstructor);

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
        $storeUser = new StoreUser();
        $storeUser->person_id = $person;
        $storeUser->username = $registerUser->phone;
        $storeUser->password = $registerUser->password;

        $newUser = $this->userRepository->createFromPerson($person, $storeUser);

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
     * @throws Exceptions\UserAlreadyRegisteredWithSamePhoneNumber
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
            switch ($registerUser->user_type) {
                case RegisterUser::TYPE_INSTRUCTOR:
                    $this->createInstructorIfNotExist($person, $registerUser);
                    break;
                case RegisterUser::TYPE_STUDENT:
                    $this->createStudentIfNotExist($person, $registerUser);
                    break;
            }

            // Create User
            $newUser = $this->createUser($person, $registerUser);

            // Auto approve new students
            if (RegisterUser::TYPE_STUDENT === $registerUser->user_type) {
                $this->userRepository->approve($newUser);
            }

            // Remove verification codes
            $this->verifyService->cleanUp($person->phone);

            $newUser->load(['person.instructor', 'person.student', 'person.customer', 'person.user']);

            \event(new UserRegisteredEvent($newUser));

            return $newUser;
        });
    }
}
