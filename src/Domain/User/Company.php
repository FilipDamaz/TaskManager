<?php

namespace App\Domain\User;

final class Company
{
    private string $name;
    private string $catchPhrase;
    private string $bs;

    private function __construct(string $name, string $catchPhrase, string $bs)
    {
        $this->name = $name;
        $this->catchPhrase = $catchPhrase;
        $this->bs = $bs;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['name'] ?? ''),
            (string) ($data['catchPhrase'] ?? ''),
            (string) ($data['bs'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'catchPhrase' => $this->catchPhrase,
            'bs' => $this->bs,
        ];
    }
}
