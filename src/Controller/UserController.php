<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function listUser(){
        return new Response('<html><body>Hello</body></html>');
    }
}