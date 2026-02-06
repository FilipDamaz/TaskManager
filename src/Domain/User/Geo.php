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

    /**
     * @param array{lat?: string, lng?: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['lat'] ?? ''),
            (string) ($data['lng'] ?? '')
        );
    }

    /**
     * @return array{lat: string, lng: string}
     */
    public function toArray(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
