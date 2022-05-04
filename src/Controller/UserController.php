<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class User
{
    public function listUser(){
        return new Response('<html><body>Hello</body></html>');
    }
}