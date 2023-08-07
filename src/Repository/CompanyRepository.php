<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Company::class);
    }

    public function checkIfRegistrationCodeExists($registrationCode): array {
        $rc_code_array = !empty($registrationCode) ? explode(",", $registrationCode) : [];

        foreach ($rc_code_array as &$val) {
            $val = (int) trim($val);
        }

        asort($rc_code_array);
        $tmp_rc_array = array_unique(array_values($rc_code_array)); // Making 'em unique

        // Look if at least one registration code is new.

        $companies = $this->findBy(['deleted' => 0, 'registration_code' => $rc_code_array]);

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
}
