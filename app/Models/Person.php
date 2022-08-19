<?php
/**
 * File: Person.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\Gender;
use App\Models\Traits\Notifiable;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

/**
 * Class Person
 * @package App\Models
 * @property string $id
 * @property string $last_name
 * @property string $first_name
 * @property string $patronymic_name
 * @property \Carbon\Carbon $birth_date
 * @property Gender $gender [male|female]
 * @property string $phone
 * @property string $email
 * @property string $picture
 * @property string $picture_thumb
 * @property string $instagram_username
 * @property string $telegram_username
 * @property string $vk_uid
 * @property string $vk_url
 * @property string $facebook_uid
 * @property string $facebook_url
 * @property string $note
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany<Customer>|null $customers
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany<Instructor>|null $instructors
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany<Student>|null $students
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany<User>|null $users
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Instructor|null $instructor
 * @property-read \App\Models\Student|null $student
 * @property-read \App\Models\User|null $user
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person query()
 * @mixin \Eloquent
 * @todo phone_verified_at, email_verified_at
 * @todo referrer_id
 * @todo source_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereFacebookUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereInstagramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person wherePatronymicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person wherePictureThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereVkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Person whereVkUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Person withAnyTags($tags, string $type = null)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Person withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Person withAnyTagsOfAnyType($tags)
 */
class Person extends Model
{
    use UsesUuid;
    use Notifiable;
    use HasTags;
    use HasFactory;
    use SoftDeletes;

    public const TABLE = 'people';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Student>|null
     */
    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class)->orderBy('created_at', 'desc');
    }

    public function getStudentAttribute(): ?Student
    {
        return $this->students?->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Customer>|null
     */
    public function customers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Customer::class)->orderBy('created_at', 'desc');
    }

    public function getCustomerAttribute(): ?Customer
    {
        return $this->customers?->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Instructor>|null
     */
    public function instructors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Instructor::class)->orderBy('created_at', 'desc');
    }

    public function getInstructorAttribute(): ?Instructor
    {
        return $this->instructors?->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<User>|null
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class)->orderBy('created_at', 'desc');
    }

    public function getUserAttribute(): ?User
    {
        return $this->users?->first();
    }

    /**
     * Get name vars in compact array
     *
     * @return array
     */
    public function compactName(): array
    {
        return [
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'patronymic_name' => $this->patronymic_name,
            'initials' => \sprintf(
                '%s. %s.',
                \mb_substr((string)$this->first_name, 0, 1),
                \mb_substr((string)$this->patronymic_name, 0, 1)
            ),
        ];
    }

    public function getNameAttribute(): string
    {
        return \trans('person.name_attribute', $this->compactName());
    }
}
