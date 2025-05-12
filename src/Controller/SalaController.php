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

/**
 * @author Miguel Gil Martínez <miguel.gil.martinez@juntadeandalucia.es>
 *
 * Página principal del sistema.
 */
class SalaController extends AbstractController {

    private BashScriptService $bashScriptService;

    public function __construct(BashScriptService $bashScriptService,
            private VlanConsejeriaRepository $vcRepo,
            private SwitchSalasRepository $ssRepo) {
        $this->bashScriptService = $bashScriptService;
    }

    #[Route('/', name: 'app_sala_index')]
    public function index(): Response {
        return $this->render('sala/index.html.twig',
                        ['salas' => $this->ssRepo->findAllSortedById(),
                            'vlans' => $this->getVlansSalas(),
                            //'vlans' => $this->bashScriptService->getVlansStatus(),
                            'consejerias' => $this->getVlanConsejerias()]);
    }

    #[Route('/toggle-sala', name: 'app_sala_toggle', methods: ['POST'])]
    public function toggleSala(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Faltan datos'],
                    Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->bashScriptService->toggleSala($data);
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
            return new JsonResponse($this->getVlansSalas());
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()],
                    Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 
     * @return array  [SalaId => Vlan];
     */
    private function getVlansSalas(): array {
        $ipSwitches = $this->ssRepo->getIPsSwitches();
        foreach ($ipSwitches as $ip) {
            $vlanStatus[$ip['ip']] = array_map(function ($i) {
                $items = explode(' ', $i);
                return [$items[0] => end($items)];
            }, explode("\n", $this->bashScriptService->getVlansStatus($ip['ip'])));
        }
        $vlanStatusSimple = [];
        foreach ($vlanStatus as $k => $v) {
            $v = array_slice($v, 0, 48);
            foreach ($v as $x) {
                $vlanStatusSimple[$k][key($x)] = reset($x);
            }
        }

        // Obtenemos los puertos de cada vlan
        $salas = $this->ssRepo->findAll();
        $salaVlan = [];
        foreach ($salas as $sala) {
            $primerPuerto = explode(' ', $sala->getPuertos())[0];
            $salaVlan[$sala->getId()] = $vlanStatusSimple[$sala->getIp()][$primerPuerto];
        }
        return $salaVlan; // [1 => 20, 2 => 30, 3 => 50, 4 => 30, 5 => 20, 6 => 50];
    }

    private function getVlanConsejerias(): array {
        return array_map(function (\App\Entity\VlanConsejeria $item) {
            return ['vlanId' => $item->getVlanid(),
                'nombre' => $item->getConsejeria()];
        }, $this->vcRepo->findAll());
    }
}
