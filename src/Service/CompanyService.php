<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Service;

use App\Constants;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompanyService {

    private CompanyRepository $companyRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CompanyRepository $companyRepository, EntityManagerInterface $entityManager) {
        $this->companyRepository = $companyRepository;
        $this->entityManager = $entityManager;
    }

    public function getCompanyList(int $pageNo = 1): array {
        $responseLimiter = Constants::RESPONSE_LIMITER;
        $offset = ($pageNo <= 1) ? 0 : (($pageNo - 1) * $responseLimiter);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
                ->select('c.name', 'c.registration_code', 'c.details', 'c.finances')
                ->from('App\Entity\Company', 'c')
                ->where('c.deleted = :deleted')
                ->setParameter('deleted', 0)
                ->setFirstResult($offset)
                ->setMaxResults($responseLimiter);

        $query = $queryBuilder->getQuery();
        $companies = $query->getResult();

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
        $previousPage = max(1, $pageNo - 1);
        $nextPage = min(ceil($totalCount / $responseLimiter), $pageNo + 1);
        $afterNextPage = min(ceil($totalCount / $responseLimiter), $nextPage + 1);

        // Return the result along with pagination information
        return [
            'companies' => $companies,
            'pagination' => [
                'currentPage' => $pageNo,
                'previousPage' => $previousPage,
                'nextPage' => (int) $nextPage,
                'afterNextPage' => (int) $afterNextPage,
                'totalCount' => (int) $totalCount,
                'totalPages' => (int) ceil($totalCount / $responseLimiter),
            ],
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

    public function update(array $formData): bool {
        $rc_code = $formData['registration_code'];
        $company = $this->companyRepository->findOneBy(['registration_code' => $rc_code]);

        if (!$company) {
            throw new NotFoundHttpException('No Company found for registration code ' . $rc_code);
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
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
