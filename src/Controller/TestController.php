<?php

namespace App\Controller;

use App\DataObject\Test;
use App\Request\TestRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    public function test(TestRequest $request)
    {
        $testData = Test::createFromRequest($request);

        dd($testData);
    }
}