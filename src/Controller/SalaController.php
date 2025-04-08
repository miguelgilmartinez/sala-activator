<?php

namespace App\Controller;

use App\Service\BashScriptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SalaController extends AbstractController
{
    private BashScriptService $bashScriptService;
    
    public function __construct(BashScriptService $bashScriptService)
    {
        $this->bashScriptService = $bashScriptService;
    }

    #[Route('/', name: 'app_sala_index')]
    public function index(): Response
    {
        $salasStatus = $this->bashScriptService->getSalasStatus();
        
        return $this->render('sala/index.html.twig', [
            'salasStatus' => $salasStatus,
        ]);
    }

    #[Route('/toggle-sala', name: 'app_sala_toggle', methods: ['POST'])]
    public function toggleSala(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $salaId = $data['sala'] ?? null;
        
        if (!$salaId) {
            return new JsonResponse(['error' => 'No se ha especificado una sala'], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $result = $this->bashScriptService->toggleSala($salaId);
            return new JsonResponse($result);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}