<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Service;

use App\Constants;
use App\Repository\CompanyRepository;
use App\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Message\ScrapMessage;
use App\Utility\ScraperUtility;
use Symfony\Component\Messenger\MessageBusInterface;

class CompanyService {

    private CompanyRepository $companyRepository;
    private MessageBusInterface $messageBusInterface;
    private EntityManagerInterface $entityManager;
    private RedisService $redisService;
    private ScraperUtility $scraperUtility;

    public function __construct(
            CompanyRepository $companyRepository,
            MessageBusInterface $messageBusInterface,
            EntityManagerInterface $entityManager,
            RedisService $redisService,
            ScraperUtility $scraperUtility
    ) {
        $this->companyRepository = $companyRepository;
        $this->messageBusInterface = $messageBusInterface;
        $this->entityManager = $entityManager;
        $this->redisService = $redisService;
        $this->scraperUtility = $scraperUtility;
    }
    
    public function add_new_company($company_details): int {

        // Extra validation just to catch duplicate reg codes which may pass in the interval of multiple consumers/workers.
        $checker = $this->companyRepository->checkIfRegistrationCodeExists($company_details['registration_code']);

        if (empty($checker)) {
            return false;
        }

        $company_id = $this->companyRepository->add_new($company_details);

        return $company_id;
    }

    public function update(array $formData): bool {
        $rc_code = $formData['registration_code'];
        $company = $this->companyRepository->findOneBy(['registration_code' => $rc_code, 'deleted' => 0]);

        if (empty($company)) {
            return false;
        }

        $result = $this->companyRepository->edit($company, $formData);
        
        return $result;
    }

    public function delete(int $registration_code): bool {
        $company = $this->companyRepository->findOneBy(['registration_code' => $registration_code, 'deleted' => 0]);

        if (empty($company)) {
            return false;
        }

        $result = $this->companyRepository->softDelete($company, $registration_code);
        
        return $result;
    }

    public function getCompanyList(int $pageNo = 1): array {

        $responseLimiter = Constants::RESPONSE_LIMITER;
        $offset = ($pageNo <= 1) ? 0 : (($pageNo - 1) * $responseLimiter);

        $redis_key = Constants::REDIS_KEY_PREFIX . $responseLimiter . "_" . $pageNo;
        $redis_pagination_key = Constants::REDIS_PAGINATE_KEY_PREFIX . $responseLimiter . "_" . $pageNo;

        if ($this->redisService->checkIfKeyExists($redis_key) && $this->redisService->checkIfKeyExists($redis_pagination_key)) {

            $redis_value = $this->redisService->getValueFromRedis($redis_key);
            $redis_pagination_value = $this->redisService->getValueFromRedis($redis_pagination_key);
            $companies = json_decode($redis_value);
            $pagination = json_decode($redis_pagination_value);
        } else {
            
            // Company Records
            $companies = $this->companyRepository->getCompanies($offset, $responseLimiter);
            $redis_value = json_encode($companies);
            $this->redisService->setValueInRedis($redis_key, $redis_value);
            
            /*
             * Used getPagination() instead of numberOfCompanies() to mainly isolate the Pagination functionalities.
             * Also, in a large DB, doctrine queryBuilder will give better performance over findBy().
             */
            $pagination = $this->companyRepository->getPagination($pageNo, $responseLimiter);
            $redis_pagination_value = json_encode($pagination);
            $this->redisService->setValueInRedis($redis_pagination_key, $redis_pagination_value);
        }

        // Return the result along with pagination information
        return [
            'companies' => $companies,
            'pagination' => $pagination
        ];
    }

    public function numberOfCompanies(): int {
        $count = count($this->companyRepository->findBy(['deleted' => 0]));

        return $count;
    }

    public function searchByRegistrationCode(string $rc_codes): array {
        
        $companies = $this->companyRepository->searchByRegCodes($rc_codes);

        return $companies;
    }

    public function scraper_service($registration_code, $cURL) {

        $filtered_rc_codes = $this->companyRepository->checkIfRegistrationCodeExists($registration_code);

        // If empty, then all codes (companies) exist already
        if (empty($filtered_rc_codes)) {
            $responseData = [
                'message' => 'Provided Registration Code(s) Already Exists',
            ];
            
            $responseData['statusCode'] = JsonResponse::HTTP_BAD_REQUEST;

            return $responseData;
        } else {
            // Else, $filtered_rc_codes['new'] will have at least one new value.
            $rc_codes = $filtered_rc_codes['new'];
            
            $formatted_cURL_data = $this->scraperUtility->processCurl($cURL);
            
            if (empty($formatted_cURL_data[Constants::COOKIE_IDENTIFIER]) ||
                empty($formatted_cURL_data[Constants::USER_AGENT_IDENTIFIER]) ||
                empty($formatted_cURL_data[Constants::CONTENT_TYPE_IDENTIFIER])
            ) {
                $responseData = [
                    'message' => "Unable to process the cURL request. Please check and try again.",
                    'statusCode' => JsonResponse::HTTP_UNAUTHORIZED // 401
                ];

                return $responseData;
            }


            foreach ($rc_codes as $rc_code) {
                // Scrapping starting in 3, 2, 1 ...
                $message = new ScrapMessage($rc_code, $formatted_cURL_data);
                $this->messageBusInterface->dispatch($message);
            }
            
            $responseData['message'] = ' Scraping Started for ' . implode(', ', $rc_codes) . " .";
            
            if (!empty($filtered_rc_codes['old'])){
                
                $msg_string = count($filtered_rc_codes['old']) > 1 ? "these values " : "this value ";
                $responseData['message'] .= PHP_EOL . "But, " . $msg_string . implode(', ', $filtered_rc_codes['old']) . " already exist.";
            }
            
            $responseData['statusCode'] = JsonResponse::HTTP_OK;

            return $responseData;
        }
    }
}
