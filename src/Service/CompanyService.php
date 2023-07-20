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

    public function getCompanyList(int $pageNo = 1) : array {
        $offset = ($pageNo == 1 || $pageNo == 0) ? 0 : (($pageNo - 1)  * Constants::RESPONSE_LIMITER + 1);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('c.name', 'c.registration_code', 'c.details', 'c.finances')
            ->from('App\Entity\Company', 'c')
            ->where('c.deleted = :deleted')
            ->setParameter('deleted', 0)
            ->setFirstResult($offset)
            ->setMaxResults(Constants::RESPONSE_LIMITER);

        $query = $queryBuilder->getQuery();
        $companies = $query->getResult();

        return $companies;
    }
    
    public function numberOfCompanies() : int {
        $count = count($this->companyRepository->findBy(['deleted' => 0]));

        return $count;
    }
}
