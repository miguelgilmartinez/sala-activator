<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\SwitchSalasRepository;

class BashScriptService {

    private string $fijarEstadoSala;
    private string $leerEstadoSala;
    private array $salaParameters = [];

    public function __construct(ParameterBagInterface $params, private SwitchSalasRepository $ssRepo) {
        // Los scripts estarán ubicados en la carpeta bin del proyecto
        $this->fijarEstadoSala = $params->get('kernel.project_dir') . '/bin/fijar_estado_sala.sh';
        $this->leerEstadoSala = $params->get('kernel.project_dir') . '/bin/leer_estado_salas.sh';
        // * PROBABLEMENTE OBSOLETO     $this->salaParameters = array_map(function ($sala) {
        //       return $sala->toArray();
        // }, $ssRepo->findAll());
    }

    /**

     * Ejecuta el script bash para activar/desactivar una sala
     */
    public function toggleSala(string $salaId): array {
        $process = new Process([$this->fijarEstadoSala, $swPlanta, $vlan]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return [
            'sala' => $salaId,
            'output' => trim($process->getOutput()),
            'success' => true
        ];
    }

    /**
     *
     * @return array vlan y primer puerto encontrado
     * @throws ProcessFailedException
     */
    public function getVlansStatus(): array {
        // Ejecuta el script leer_estado_salas pasando la IP del switch
        $ips = $this->ssRepo->getIPsSwitches();
        foreach ($ips as $ip) {
            $resultado[$ip['ip']] = [];
            $process = new Process([$this->leerEstadoSala, $ip['ip']]);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Dividir línea por línea
            foreach (explode("\n", $process->getOutput()) as $vlan_puerto) {
                // Eliminar espacios extra y saltar líneas vacías
                $vlan_puerto = trim($vlan_puerto);
                if ($vlan_puerto === '')
                    continue;
                // Separar por espacios múltiples o tabulaciones
                preg_match('/^\s*(\S+)\s+(\S+)(.*)$/', $vlan_puerto, $matches);
                if (isset($matches[1], $matches[2])) {
                    $vlan = $matches[1];
                    $puerto = $matches[2];
                    // Si no existe el grupo, crearlo
                    if (!isset($resultado[$ip['ip']][$vlan])) {
                        $resultado[$ip['ip']][$vlan] = $puerto;
                    }
                }
            }
        }
        return $resultado;
    }
}
