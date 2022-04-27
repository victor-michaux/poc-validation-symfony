<?php

namespace App\Request;

interface ValidationRequest
{
    public function rules(): array;
}