<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class StoreBookRequest implements ValidationRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    public $title;

    /**
     * @Assert\Length(min=10)
     */
    public $description;
}