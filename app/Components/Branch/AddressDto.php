<?php

namespace App\Components\Branch;

class AddressDto
{
    public ?string $country;
    public ?string $city;
    public ?string $street;
    public ?string $building;
    public ?array $coordinates;

    public function __construct(array $data = [])
    {
        $this->country = $data['country'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->street = $data['street'] ?? null;
        $this->building = $data['building'] ?? null;
        $this->coordinates = $data['coordinates'] ?? null;
    }


    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}