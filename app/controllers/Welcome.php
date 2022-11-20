<?php

namespace App\Controllers;

use App\src\Objects\Controller;

class Welcome extends Controller
{
    public function indexGet( )
    {
        $this->response(
            [
                "message" => "Tutto funziona correttamente"
            ],
            200
        );
    }
}