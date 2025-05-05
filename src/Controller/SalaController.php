<?php

namespace App\Controller;

use App\Service\BashScriptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SwitchSalasRepository;
use App\Repository\VlanConsejeriaRepository;

class SalaController extends AbstractController {

    private BashScriptService $bashScriptService;

    public function __construct(BashScriptService $bashScriptService,
            private VlanConsejeriaRepository $vcRepo,
            private SwitchSalasRepository $ssRepo) {
        $this->bashScriptService = $bashScriptService;
    }

    #[Route('/', name: 'app_sala_index')]
    public function index(): Response {
//       $salasStatus = array_map(function (SwitchSalas $sala) {
//            return $sala->toArray();
//        }, $this->ssRepo->findAll());
        return $this->render('sala/index.html.twig',
                        ['salas' => $this->ssRepo->findAll(),
                            'vlans' => $this->bashScriptService->getVlansStatus(),
                            'consejerias' => $this->getVlanConsejerias()]);
    }

    #[Route('/toggle-sala', name: 'app_sala_toggle', methods: ['POST'])]
    public function toggleSala(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $salaId = $data['sala'] ?? null;

        if (!$salaId) {
            return new JsonResponse(['error' => 'No se ha especificado una sala'],
                    Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->bashScriptService->toggleSala($salaId);
            return new JsonResponse($result);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()],
                    Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/get-salas-status', name: 'app_sala_status', methods: ['GET'])]
    public function getSalasStatus(): JsonResponse {
        try {
            //$salasStatus = $this->bashScriptService->getSalasStatus();
            return new JsonResponse($salasStatus);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()],
                    Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getVlanConsejerias(): array {

        return array_map(function (\App\Entity\VlanConsejeria $item) {
            return ['vlanId' => $item->getVlanid(),
                'nombre' => $item->getConsejeria()];
        }, $this->vcRepo->findAll());
    }
}
