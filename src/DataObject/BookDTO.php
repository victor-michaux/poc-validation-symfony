<?php

namespace App\DataObject;

use App\Request\StoreBookRequest;

class BookDTO
{
    private string $title;
    private ?string $description;

    public function __construct(array $values)
    {
        $this->title = $values['title'];
        $this->description = $values['description'];
    }

    static function createFromRequest(StoreBookRequest $request): self
    {
        return new self(
            [
                'title' => $request->title,
                'description' => $request->description,
            ]
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}