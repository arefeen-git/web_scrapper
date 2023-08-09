<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Message;

class ScrapMessage
{
    private $registrationCode;
    private $curlData;

    public function __construct(string $registrationCode, array $curlData)
    {
        $this->registrationCode = $registrationCode;
        $this->curlData = $curlData;
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }

    public function getCurlData(): array
    {
        return $this->curlData;
    }
}

