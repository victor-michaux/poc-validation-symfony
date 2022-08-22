<?php

namespace App\Controller;

use App\DataObject\BookDTO;
use App\Request\StoreBookRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    public function store(StoreBookRequest $request)
    {
        $bookDTO = BookDTO::createFromRequest($request);

        dd($bookDTO);
    }
}