<?php

namespace App\Http\Controllers;

class PhpInfoController extends Controller
{
    public function __invoke(): void
    {
        phpinfo();
    }
}
