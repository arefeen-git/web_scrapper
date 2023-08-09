<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Message;

class ScrapMessage
{
    private $registrationCode;
<<<<<<< HEAD
    private $curlData;

    public function __construct(string $registrationCode, array $curlData)
    {
        $this->registrationCode = $registrationCode;
        $this->curlData = $curlData;
=======
    private $cookieConsent;

    public function __construct(string $registrationCode, string $cookieConsent)
    {
        $this->registrationCode = $registrationCode;
        $this->cookieConsent = $cookieConsent;
>>>>>>> develop
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }

<<<<<<< HEAD
    public function getCurlData(): array
    {
        return $this->curlData;
=======
    public function getCookieConsent(): string
    {
        return $this->cookieConsent;
>>>>>>> develop
    }
}

