<?php
/**
 * File: StoreBranchRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Branch;
use App\Repository\BranchRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreBranchRequest
 * @package App\Http\Requests\ManagerApi
 */
class StoreBranchRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique(Branch::TABLE)->ignore($this->getId())
            ],
            'summary' => [
                'nullable',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
            ],
            'email' => [
                'nullable',
                'string',
            ],
            'url' => [
                'nullable',
                'string',
            ],
            'vk_url' => [
                'nullable',
                'string',
            ],
            'facebook_url' => [
                'nullable',
                'string',
            ],
            'telegram_username' => [
                'nullable',
                'string',
            ],
            'instagram_username' => [
                'nullable',
                'string',
            ],
            'address' => [
                'nullable',
                'array',
            ],
            'address.country' => [
                'nullable',
                'string',
            ],
            'address.city' => [
                'nullable',
                'string',
            ],
            'address.street' => [
                'nullable',
                'string',
            ],
            'address.building' => [
                'nullable',
                'string',
            ],
            'address.coordinates' => [
                'nullable',
                'array',
                'min:2',
                'max:2',
            ],
            'address.coordinates.*' => [
                'required_with:address.coordinates',
                'numeric',
            ],
            'number' => [
                'nullable',
                'int',
                'min:0'
            ],
        ];
    }

    /**
     * @return DTO\StoreBranch
     */
    public function getDto(): DTO\StoreBranch
    {
        $validated = $this->validated();
        $dto = new DTO\StoreBranch;

        $dto->user = $this->user();
        $dto->name = $validated['name'];
        $dto->summary = $validated['summary'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->phone = $validated['phone'] ?? null;
        $dto->email = $validated['email'] ?? null;
        $dto->url = $validated['url'] ?? null;
        $dto->vk_url = $validated['vk_url'] ?? null;
        $dto->facebook_url = $validated['facebook_url'] ?? null;
        $dto->telegram_username = $validated['telegram_username'] ?? null;
        $dto->instagram_username = $validated['instagram_username'] ?? null;
        $dto->address = $validated['address'] ?? [];
        $dto->number = isset($validated['number']) ? (int)$validated['number'] : $this->getNextNumberValue();

        return $dto;
    }

    /**
     * @return int
     */
    private function getNextNumberValue(): int
    {
        /** @var BranchRepository $repository */
        $repository = \app(BranchRepository::class);
        return $repository->getNextNumberValue();
    }

    protected function getId(): ?string
    {
        return $this->route()->parameter('id');
    }
}
