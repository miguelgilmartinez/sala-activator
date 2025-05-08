<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\SwitchSalasRepository;
use Psr\Log\LoggerInterface;

class BashScriptService {

    private string $fijarEstadoSala;
    private string $leerEstadoSala;

    public function __construct(ParameterBagInterface $params,
            private LoggerInterface $l, private SwitchSalasRepository $ssRepo) {
        // Scripts ubicados en la carpeta bin del proyecto
        $this->fijarEstadoSala = $params->get('kernel.project_dir') . '/bin/fijar_estado_sala.sh';
        $this->leerEstadoSala = $params->get('kernel.project_dir') . '/bin/leer_estado_salas.sh';
    }

    /**
     * Ejecuta el script bash para activar/desactivar una sala
     */
    public function toggleSala(array $dataJson): array {
        $this->l->info(json_encode($dataJson));
        $switchSala = $this->ssRepo->find($dataJson['salaId']);
        $process = new Process([$this->fijarEstadoSala, $switchSala->getIp(),
            $dataJson['vlan'], $switchSala->getPuertos()]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return [
            'sala' =>$dataJson['salaId'],
            'vlan' =>  $dataJson['vlan'],
            'output' => trim($process->getOutput()),
            'success' => true
        ];
    }

    /**
     * @return array vlan y primer puerto encontrado
     * @throws ProcessFailedException
     */
    public function getVlansStatus(string $switchIP): string {
// Ejecuta el script leer_estado_salas pasando la IP del switch
        $process = new Process([$this->leerEstadoSala, $switchIP]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}
