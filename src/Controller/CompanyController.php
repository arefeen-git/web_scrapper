<?php

namespace App\Controller;

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
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(
            EntityManagerInterface $entityManager,
            ManagerRegistry $doctrine,
            CompanyService $companyService,
            TokenGeneratorInterface $tokenGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
        $this->companyService = $companyService;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
    * This method returns a list of companies obtained through a search operation.
    *
    * @param Request $request The incoming request object.
    *
    * @return Response Returns a rendered Twig file containing company data, pagination, CSRF token, and messages.
    *
    * @throws InvalidArgumentException If the CSRF token validation fails.
    */
    #[Route('/company/list/{pageNo<\d+>?}', name: 'app_company_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response {

        // Search Request
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('csrf_token');
            $csrfToken = $request->getSession()->get('company_item_csrf_token');

            if ($submittedToken == $csrfToken) {
                $token = $csrfToken; // For loading the page.
                $rcCodes = $request->request->get('rc-codes');
                $companies = $this->companyService->searchByRegistrationCode($rcCodes);
                $pagination = [];
            } else {

                return $this->render('company/index.html.twig', [
                            'companies' => [],
                            'pagination' => [],
                            'csrf_token' => $csrfToken, // Setting this, just in case the user don't check the url and falls into a loop.
                            'msg' => 'CSRF Token Mismatch.' // Multiversal Request Not Allowed!!! Contact Doromammu for Bargain.
                ]);
            }
        } else {
            // Check if token exist
            $token = $request->getSession()->get('company_item_csrf_token');
            
            if (empty($token)) {
                // Genrate CSRF Token
                $token = $this->tokenGenerator->generateToken();
                $request->getSession()->set('company_item_csrf_token', $token);
            }

            // Get the pageNo from the route parameters.
            $pageNo = (int) $request->attributes->get('pageNo', 1);

            // If pageNo is 0, redirect to pageNo 1.
            if (empty($pageNo)) {
                return $this->redirectToRoute('app_company_index', ['pageNo' => 1]);
            }

            $company_count = $this->companyService->numberOfCompanies();

            if (empty($company_count)) {
                return $this->redirectToRoute('app_company_new');
            }

            $company_list_details = $this->companyService->getCompanyList($pageNo);

            $companies = $company_list_details['companies'];
            $pagination = !empty($companies) ? $company_list_details['pagination'] : [];
        }

        $msg = !empty($request->getSession()->get('msg')) ? $request->getSession()->get('msg') : null;

        if (!empty($msg)) {
            $request->getSession()->remove('msg');
        }

        return $this->render('company/index.html.twig', [
                    'companies' => $companies,
                    'pagination' => $pagination,
                    'csrf_token' => $token,
                    'msg' => $msg
        ]);
    }

    /**
    * This method handles the scraping of new companies using provided registration code(s).
    * 
    * @param Request $request The incoming request object containing registration code(s).
    *
    * @return JsonResponse Returns a JSON response with the result of the scraping operation.
    */
    #[Route('/scrap', name: 'app_company_scrap', methods: ['POST'])]
    public function scrap(Request $request): JsonResponse {

        $submittedToken = $request->request->get('csrf_token');
        $csrfToken = $request->getSession()->get('company_item_csrf_token');

        if ($submittedToken !== $csrfToken) {
            $responseData = [
                'message' => 'CSRF Token Mismatch.'
            ];

            $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401
            return new JsonResponse($responseData, $statusCode);
        } else {
            $registration_code = $request->request->get('rc-code');

            $responseData = $this->companyService->scraper_service($registration_code);

            return new JsonResponse($responseData, $responseData['statusCode']);
        }
    }

    /**
    * This method handles the rendering of the new company submission form.
    * 
    * @param Request $request The incoming request object.
    *
    * @return Response Returns a response containing the rendered form with CSRF token.
    */
    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        
        // Check if token exist
        $token = $request->getSession()->get('company_item_csrf_token');

        if (empty($token)) {
            // Genrate CSRF Token
            $token = $this->tokenGenerator->generateToken();
            $request->getSession()->set('company_item_csrf_token', $token);
        }

        return $this->render('company/new.html.twig', [
                    'csrf_token' => $token
        ]);
    }

    /**
    * This method handles the editing of a company's information.
    * 
    * @param Request $request The incoming request object.
    *
    * @return Response Returns a rendered Twig file containing company data, CSRF token, and messages.
    */
    #[Route('/edit/{registration_code}', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response {
        $rcCode = $request->attributes->get('registration_code');

        if ($request->isMethod('POST') && !empty($rcCode)) {

            $submittedToken = $request->request->get('csrf_token');
            $csrfToken = $request->getSession()->get('company_item_csrf_token');

            // Match the CSRF tokens
            if ($submittedToken == $csrfToken) {
                $formData = $request->request->all();

                unset($formData['csrf_token']);

                $formData['registration_code'] = $rcCode;

                // Update company information
                if ($this->companyService->update($formData)) {
                    $request->getSession()->set('msg', 'Successfully Updated.');
                    return $this->redirectToRoute('app_company_index', ['pageNo' => 1]);
                } else {
                    $request->getSession()->set('msg', 'Update Failed.');
                    return $this->redirectToRoute('app_company_edit', ['registration_code' => $rcCode]);
                }
            } else {
                $request->getSession()->set('msg', 'Update Failed: Token Mismatch.');
                return $this->redirectToRoute('app_company_edit', ['registration_code' => $rcCode]);
            }

            return $this->redirectToRoute('app_company_index', ['pageNo' => 1]);
        } else {
            
            // Check if token exist
            $token = $request->getSession()->get('company_item_csrf_token');

            if (empty($token)) {
                // Genrate CSRF Token
                $token = $this->tokenGenerator->generateToken();
                $request->getSession()->set('company_item_csrf_token', $token);
            }

            if (!empty($rcCode)) {
                $company = $this->companyService->searchByRegistrationCode($rcCode);
            } else {
                return $this->redirectToRoute('app_company_index', ['pageNo' => 1]);
            }
        }

        $msg = !empty($request->getSession()->get('msg')) ? $request->getSession()->get('msg') : null;

        if (!empty($msg)) {
            $request->getSession()->remove('msg');
        }

        return $this->render('company/edit.html.twig', [
                    'company' => !empty($company[0]) ? $company[0] : [],
                    'csrf_token' => $token,
                    'msg' => $msg
        ]);
    }

    /**
    * This method handles the deletion (soft) of a company.
    * 
    * @param Request $request The incoming request object.
    *
    * @return JsonResponse Returns a JSON response containing the deletion status message.
    */
    #[Route('/delete/{registration_code}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request): JsonResponse {
        $submittedToken = $request->request->get('csrf_token');
        $csrfToken = $request->getSession()->get('company_item_csrf_token');

        if ($submittedToken !== $csrfToken) {

            $responseData = [
                'message' => 'CSRF Token Mismatch.'
            ];

            $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401
        } else {

            $rcCode = $request->attributes->get('registration_code');

            if ($this->companyService->delete($rcCode)) {
                $responseData = [
                    'message' => 'Deleted (soft) successfully.'
                ];
                $statusCode = JsonResponse::HTTP_OK;
            } else {
                $responseData = [
                    'message' => 'Operation failed.',
                ];

                // Set the appropriate status code.
                $statusCode = JsonResponse::HTTP_BAD_REQUEST;
            }

            return new JsonResponse($responseData, $statusCode);
        }
    }
}
