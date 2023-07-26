<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Message;

class ScrapMessage
{
    private $registrationCode;
    private $cookieConsent;

    public function __construct(string $registrationCode, string $cookieConsent)
    {
        $this->registrationCode = $registrationCode;
        $this->cookieConsent = $cookieConsent;
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }

    public function getCookieConsent(): string
    {
        return $this->cookieConsent;
    }
}

