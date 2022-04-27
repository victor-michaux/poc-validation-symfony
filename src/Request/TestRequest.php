<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class TestRequest implements ValidationRequest
{
    private Request $request;

    public function rules(): array
    {
        return [
            'title' => new Required(
                [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ]
            ),
            'description' => new Optional(
                [
                    new Length(['min' => 10])
                ]
            )
        ];
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}