<?php

namespace App\Domain\User;

final class Geo
{
    private string $lat;
    private string $lng;

    private function __construct(string $lat, string $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['lat'] ?? ''),
            (string) ($data['lng'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
