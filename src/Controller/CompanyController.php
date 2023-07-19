<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Utility\ScraperUtility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/company')]
class CompanyController extends AbstractController {

    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response {
        return $this->render('company/index.html.twig', [
                    'companies' => $companyRepository->findAll(),
        ]);
    }

    #[Route('/scrap', name: 'app_company_scrap', methods: ['POST'])]
    public function scrap(Request $request): JsonResponse {

        $submittedToken = $request->request->get('csrf_token');
        $csrfToken = $request->getSession()->get('company_item_csrf_token');
        
        if ($submittedToken !== $csrfToken) {
            $responseData = [
                'message' => 'Multiversal Request Not Allowed!!! Contact Doromammu for Bargain',
            ];
            
            $statusCode = JsonResponse::HTTP_UNAUTHORIZED; // 401
            return new JsonResponse($responseData, $statusCode);
            
        } else {
            $cookie_consent = $request->request->get('cookie-consent');
            $rc_code = $request->request->get('rc-code');

            $scraperUtility = new ScraperUtility();
            $company_details = $scraperUtility->start_scraping($rc_code, $cookie_consent);

            $responseData = [
                'message' => 'Scraping completed successfully',
                'company_details' => $company_details
            ];

            // Set the appropriate status code
            $statusCode = JsonResponse::HTTP_OK; // 200
            return new JsonResponse($responseData, $statusCode);
        }
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        
        // Generate and store the CSRF token in the session
        $token = $tokenGenerator->generateToken();
        $request->getSession()->set('company_item_csrf_token', $token);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

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
