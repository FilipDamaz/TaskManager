<?php

namespace App\Domain\User;

final class Address
{
    private string $street;
    private string $suite;
    private string $city;
    private string $zipcode;
    private Geo $geo;

    private function __construct(string $street, string $suite, string $city, string $zipcode, Geo $geo)
    {
        $this->street = $street;
        $this->suite = $suite;
        $this->city = $city;
        $this->zipcode = $zipcode;
        $this->geo = $geo;
    }

    /**
     * @param array{street?: string, suite?: string, city?: string, zipcode?: string, geo?: array{lat?: string, lng?: string}} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['street'] ?? ''),
            (string) ($data['suite'] ?? ''),
            (string) ($data['city'] ?? ''),
            (string) ($data['zipcode'] ?? ''),
            Geo::fromArray((array) ($data['geo'] ?? []))
        );
    }

    /**
     * @return array{street: string, suite: string, city: string, zipcode: string, geo: array{lat: string, lng: string}}
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'suite' => $this->suite,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'geo' => $this->geo->toArray(),
        ];
    }
}
