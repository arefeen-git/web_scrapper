<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Utility\ScraperUtility;
use App\Service\CompanyService;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/company')]
class CompanyController extends AbstractController {

    private EntityManagerInterface $entityManager;
    private ManagerRegistry $doctrine;
    private CompanyService $companyService;

    public function __construct(
            EntityManagerInterface $entityManager,
            ManagerRegistry $doctrine,
            CompanyService $companyService
    ) {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
        $this->companyService = $companyService;
    }

    #[Route('/company/{pageNo<\d+>?}', name: 'app_company_index', methods: ['GET'])]
    public function index(Request $request, CompanyService $companyService): Response {
        // Get the pageNo from the route parameters
        $pageNo = (int) $request->attributes->get('pageNo', 1);

        // If pageNo is 0, redirect to pageNo 1
        if ($pageNo === 0) {
            return $this->redirectToRoute('app_company_index', ['pageNo' => 1]);
        }
        $companies = $companyService->getCompanyList($pageNo);
        $company_count = $companyService->numberOfCompanies();
        
        if(empty($company_count)){
            return $this->redirectToRoute('app_company_new');
        }
        
        return $this->render('company/index.html.twig', [
                    'companies' => $companies
        ]);
    }

    #[Route('/scrap', name: 'app_company_scrap', methods: ['POST'])]
    public function scrap(Request $request): JsonResponse {

        $submittedToken = $request->request->get('csrf_token');
        $csrfToken = $request->getSession()->get('company_item_csrf_token');

        if ($submittedToken !== $csrfToken) {
            $responseData = [
                'message' => 'CSRF Token Mismatch : Multiversal Request Not Allowed!!! Contact Doromammu for Bargain',
            ];

            $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401
            return new JsonResponse($responseData, $statusCode);
        } else {
            $cookie_consent = $request->request->get('cookie-consent');
            $rc_code = $request->request->get('rc-code');

            // Get the CompanyRepository from the container.
            $companyRepository = $this->doctrine->getRepository(Company::class);

            // Check if the registration code already exists
            if ($companyRepository->isRegistrationCodeExists($rc_code)) {
                $responseData = [
                    'message' => 'Company Already Exists',
                ];

                // Set the appropriate status code.
                $statusCode = JsonResponse::HTTP_BAD_REQUEST; // 200
                return new JsonResponse($responseData, $statusCode);
            }

            $scraperUtility = new ScraperUtility();
            $company_details = $scraperUtility->start_scraping($rc_code, $cookie_consent);

            $store_new = $this->add_new($company_details);

            $responseData = [
                'message' => 'Scraping completed successfully',
                'company_id' => $store_new
            ];

            // Set the appropriate status code
            $statusCode = JsonResponse::HTTP_OK; // 200
            return new JsonResponse($responseData, $statusCode);
        }
    }

    public function add_new($company_details): int {
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

        $company_id = $company->getId();

        return $company_id;
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TokenGeneratorInterface $tokenGenerator): Response {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        // Generate and store the CSRF token in the session
        $token = $tokenGenerator->generateToken();
        $request->getSession()->set('company_item_csrf_token', $token);

        return $this->render('company/new.html.twig', [
                    'company' => $company,
                    'form' => $form,
                    'csrf_token' => $token
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response {
        return $this->render('company/show.html.twig', [
                    'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/edit.html.twig', [
                    'company' => $company,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
