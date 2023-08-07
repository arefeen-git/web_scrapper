<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\MessageHandler;

use App\Message\ScrapMessage;
use App\Utility\ScraperUtility;
use App\Service\CompanyService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ScrapMessageHandler implements MessageHandlerInterface
{
    private $scraperUtility;
    private $companyService;

    public function __construct(ScraperUtility $scraperUtility, CompanyService $companyService)
    {
        $this->scraperUtility = $scraperUtility;
        $this->companyService = $companyService;
    }

    public function __invoke(ScrapMessage $message)
    {
        $registrationCode = $message->getRegistrationCode();
        $cookieConsent = $message->getCookieConsent();

        $company_details = $this->scraperUtility->start_scraping($registrationCode, $cookieConsent);
        $store_new = !empty($company_details) ? $this->companyService->add_new_company($company_details) : false;
        
        if (!empty($store_new)){
            echo "Reg Code : " . $registrationCode . " Stored in ID : " . $store_new . " . ";
        }
        else{
            echo "Reg Code : " . $registrationCode . " already exists / Provided Cookie Consent not working. Search company list to check if company already exists. ";
        }
    }
}

