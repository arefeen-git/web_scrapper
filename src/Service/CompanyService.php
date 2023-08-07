<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Service;

use App\Constants;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Message\ScrapMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class CompanyService {

    private CompanyRepository $companyRepository;
    private MessageBusInterface $messageBusInterface;
    private EntityManagerInterface $entityManager;
    private RedisService $redisService;

    public function __construct(
            CompanyRepository $companyRepository,
            MessageBusInterface $messageBusInterface,
            EntityManagerInterface $entityManager,
            RedisService $redisService
    ) {
        $this->companyRepository = $companyRepository;
        $this->messageBusInterface = $messageBusInterface;
        $this->entityManager = $entityManager;
        $this->redisService = $redisService;
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

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                    ->select('c.name', 'c.registration_code', 'c.details', 'c.finances')
                    ->from('App\Entity\Company', 'c')
                    ->where('c.deleted = :deleted')
                    ->setParameter('deleted', 0)
                    ->orderBy('c.id', 'ASC')
                    ->setFirstResult($offset)
                    ->setMaxResults($responseLimiter);

            $query = $queryBuilder->getQuery();
            $companies = $query->getResult();

            $redis_value = json_encode($companies);

            $this->redisService->setValueInRedis($redis_key, $redis_value);

            // *** Pagination Calculation Starts Here. ***
            // Count total number of companies
            $totalCount = $this->entityManager
                    ->createQueryBuilder()
                    ->select('COUNT(c.id)')
                    ->from('App\Entity\Company', 'c')
                    ->where('c.deleted = :deleted')
                    ->setParameter('deleted', 0)
                    ->getQuery()
                    ->getSingleScalarResult();

            // Calculate previous, next, and after next page numbers
            $pagination = [];
            $pagination['currentPage'] = $pageNo;
            $pagination['previousPage'] = (int) max(1, $pageNo - 1);
            $pagination['nextPage'] = (int) min(ceil($totalCount / $responseLimiter), $pageNo + 1);
            $pagination['afterNextPage'] = (int) min(ceil($totalCount / $responseLimiter), $pagination['nextPage'] + 1);
            $pagination['totalCount'] = (int) $totalCount;
            $pagination['totalPages'] = (int) ceil($totalCount / $responseLimiter);

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

        $rc_code_array = !empty($rc_codes) ? explode(",", $rc_codes) : [];

        foreach ($rc_code_array as $key => &$val) {
            $val = (int) trim($val);
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();

        if (empty($rc_code_array)) {
            $queryBuilder
                    ->select('c.name', 'c.registration_code', 'c.details', 'c.finances')
                    ->from('App\Entity\Company', 'c')
                    ->where('c.deleted = :deleted')
                    ->setParameter('deleted', 0)
                    ->setMaxResults(Constants::RESPONSE_LIMITER);
        } else {
            $queryBuilder
                    ->select('c.name', 'c.registration_code', 'c.details', 'c.finances')
                    ->from('App\Entity\Company', 'c')
                    ->where('c.deleted = :deleted')
                    ->setParameter('deleted', 0);

            if (!empty($rc_codes)) {
                $queryBuilder
                        ->andWhere($queryBuilder->expr()->in('c.registration_code', $rc_code_array));
            }
        }

        $query = $queryBuilder->getQuery();
        $companies = $query->getResult();

        return $companies;
    }

    public function scraper_service($registration_code, $cookie_consent) {
        
        $filtered_rc_codes = $this->companyRepository->checkIfRegistrationCodeExists($registration_code);

        // If empty, then all codes (companies) exist already
        if (empty($filtered_rc_codes)) {
            $responseData = [
                'message' => 'Provided Registration Code(s) Already Exists',
            ];
            
            $responseData['statusCode'] = JsonResponse::HTTP_BAD_REQUEST;

            return $responseData;
        } else {
            $rc_codes = $filtered_rc_codes['new'];

            foreach ($rc_codes as $rc_code) {
                // Scrapping starting in 3, 2, 1 ...
                $message = new ScrapMessage($rc_code, $cookie_consent);
                $this->messageBusInterface->dispatch($message);
            }
            
            $responseData['message'] = ' Scraping Started for ' . implode(', ', $rc_codes);
            $responseData['statusCode'] = JsonResponse::HTTP_OK;

            return $responseData;
        }
    }

    public function add_new_company($company_details): int {
        
        // Extra validation just to catch duplicate reg codes which may pass in the interval of multiple consumers/workers.
        $checker = $this->companyRepository->checkIfRegistrationCodeExists($company_details['registration_code']);
        
        if (empty($checker)){
            return false;
        }
        
        // Create a new Company entity and set its properties
        $company = new Company();
        $company->setRegistrationCode($company_details['registration_code']);
        $company->setName($company_details['name']);

        $details = [
            "vat" => $company_details['vat'],
            "address" => $company_details['address'],
            "mobile" => $company_details['mobile']
        ];

        $company->setDetails($details);
        $company->setFinances($company_details['finances']);
        $company->setDeleted(0);

        $this->entityManager->persist($company);
        $this->entityManager->flush();
        $this->redisService->deleteAll();

        $company_id = $company->getId();

        return $company_id;
    }

    public function update(array $formData): bool {
        $rc_code = $formData['registration_code'];
        $company = $this->companyRepository->findOneBy(['registration_code' => $rc_code, 'deleted' => 0]);

        if (empty($company)) {
            return false;
        }

        $company->setName($formData['name']);

        $details = $company->getDetails();
        $details['vat'] = $formData['vat'];
        $details['address'] = $formData['address'];
        $company->setDetails($details);

        $currentDateTime = new \DateTimeImmutable();
        $company->setUpdatedAt($currentDateTime);

        try {
            $this->entityManager->flush();
            $this->redisService->deleteAll();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(int $registration_code): bool {
        $company = $this->companyRepository->findOneBy(['registration_code' => $registration_code, 'deleted' => 0]);

        if (empty($company)) {
            return false;
        }
        
        // Setting temporary registration code, otherwise if someone want to store deleted registration code
        // an error would be given as the schema declares registration_code as a unique field.
        
        $dateTime = new \DateTime();
        $day = $dateTime->format('d');
        $hour = $dateTime->format('H');
        $minute = $dateTime->format('i');
        $second = $dateTime->format('s');
        
        $tmp_rc = rand(1,9) . $day . $hour . $minute . $second;
        $company->setRegistrationCode($tmp_rc);
        
        // Storing original rc for future reference
        $details = $company->getDetails();
        $details['original_registration_code'] = $registration_code;
        $company->setDetails($details);
        
        $company->setDeleted(true);
        $currentDateTime = new \DateTimeImmutable();
        $company->setDeletedAt($currentDateTime);

        try {
            $this->entityManager->flush();
            $this->redisService->deleteAll();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
