<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Message;

class ScrapMessage
{
    private $registrationCode;

    public function __construct(string $registrationCode)
    {
        $this->registrationCode = $registrationCode;
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }
}

