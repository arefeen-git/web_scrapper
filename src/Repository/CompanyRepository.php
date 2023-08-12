<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository {

    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager) {
        parent::__construct($registry, Company::class);
        $this->entityManager = $entityManager;
    }

    public function checkIfRegistrationCodeExists($registrationCode): array {
        $rc_code_array = !empty($registrationCode) ? explode(",", $registrationCode) : [];

        foreach ($rc_code_array as &$val) {
            $val = (int) trim($val);
        }

        asort($rc_code_array);
        $tmp_rc_array = array_unique(array_values($rc_code_array)); // Making 'em unique
        
        // Look if at least one registration code is new.
        $companies = $this->findBy(['deleted' => 0, 'registration_code' => $tmp_rc_array]);

        $existing_registration_codes = array_map(function ($company) {
            return $company->getRegistrationCode();
        }, $companies);

        asort($existing_registration_codes);
        $tmp_existing_array = array_values($existing_registration_codes);

        $result = [];

        if ($tmp_rc_array === $tmp_existing_array) {
            return $result;
        } else {
            $result['new'] = array_diff($tmp_rc_array, $tmp_existing_array);
            $result['old'] = $tmp_existing_array;

            return $result;
        }
    }

    public function getCompanies($offset, $responseLimiter) {
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

        return $companies;
    }

    public function getPagination($pageNo, $responseLimiter) {
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

        return $pagination;
    }

    public function searchByRegCodes($rc_codes) {
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
}
