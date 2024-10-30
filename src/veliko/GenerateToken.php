<?php

namespace App\veliko;

class GenerateToken
{
    public function create():string
    {
        return bin2hex(random_bytes(32));
    }
}