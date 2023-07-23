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

class CompanyService {

    private CompanyRepository $companyRepository;
    private EntityManagerInterface $entityManager;
    private RedisService $redisService;

    public function __construct(CompanyRepository $companyRepository, EntityManagerInterface $entityManager, RedisService $redisService) {
        $this->companyRepository = $companyRepository;
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
    
    public function add_new_company($company_details): int {
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
