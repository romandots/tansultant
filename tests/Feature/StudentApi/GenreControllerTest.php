<?php
/**
 * File: GenreControllerTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\StudentApi;

use App\Models\Genre;
use App\Services\Permissions\UserRoles;
use Spatie\Tags\Tag;
use Tests\TestCase;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

class GenreControllerTest extends TestCase
{
    use CreatesFakePerson;
    use CreatesFakeUser;

    private const URL = '/student/genres';

    private \App\Models\User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createFakeUser([], [], [UserRoles::STUDENT]);
        $this->actingAs($this->user);

        $genres = [
            'hip-hop',
            'dancehall',
            'house',
        ];
        Tag::findOrCreate($genres, Genre::class);
    }

    public function testIndex(): void
    {
        $this
            ->get(self::URL)
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'data' => [
                    'hip-hop',
                    'dancehall',
                    'house',
                ]
            ]);
    }

    public function testGetSubscriptions(): void
    {
        $this->user->person->syncTagsWithType(['hip-hop', 'house'], Genre::class);

        $this
            ->get(self::URL . '/my')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    'hip-hop',
                    'house',
                ]
            ]);
    }

    public function testSubscribeNotFound(): void
    {
        $url = self::URL . '/contemporary';
        $this
            ->post($url)
            ->assertNotFound();
    }

    public function testSubscribe(): void
    {
        $url = self::URL . '/hip-hop';
        $this
            ->post($url)
            ->assertOk();

        $tags = $this->user->person->tags->pluck('name')->all();
        $this->assertEquals(['hip-hop'], $tags);

        $url = self::URL . '/house';
        $this
            ->post($url)
            ->assertOk();

        $this->user->person->refresh();
        $tags = $this->user->person->tags->pluck('name')->all();
        $this->assertEquals(['hip-hop', 'house'], $tags);
    }

    public function testUnsubscribe(): void
    {
        $this->user->person->syncTagsWithType(['hip-hop', 'house'], Genre::class);

        $url = self::URL . '/hip-hop';
        $this
            ->delete($url)
            ->assertOk();

        $tags = $this->user->person->tags->pluck('name')->all();
        $this->assertEquals(['house'], $tags);

        $url = self::URL . '/house';
        $this
            ->delete($url)
            ->assertOk();

        $this->user->person->refresh();
        $tags = $this->user->person->tags->pluck('name')->all();
        $this->assertEquals([], $tags);
    }
}
