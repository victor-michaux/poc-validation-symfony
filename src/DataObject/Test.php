<?php

namespace App\DataObject;

use App\Request\TestRequest;

class Test
{
    private string $title;
    private ?string $description;

    public function __construct(array $values)
    {
        $this->title = $values['title'];
        $this->description = $values['description'];
    }

    static function createFromRequest(TestRequest $request): self
    {
        return new self(
            [
                'title' => $request->getRequest()->request->get('title'),
                'description' => $request->getRequest()->request->get('description'),
            ]
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}