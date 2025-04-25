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

    public function __construct(ParameterBagInterface $params, SwitchSalasRepository $ssRepo) {
        // Los scripts estarán ubicados en la carpeta bin del proyecto
        $this->fijarEstadoSala = $params->get('kernel.project_dir') . '/bin/fijar_estado_sala.sh';
        $this->leerEstadoSala = $params->get('kernel.project_dir') . '/bin/leer_estado_salas.sh';
        $this->salaParameters = array_map(function ($sala) {
            return $sala->toArray();
        }, $ssRepo->findAll());
    }

    /**
     * Ejecuta el script bash para activar/desactivar una sala
     */
    public function toggleSala(string $salaId): array {
        if (!isset($this->salaParameters[$salaId])) {
            throw new \InvalidArgumentException("Sala no válida: $salaId");
        }

        [$swPlanta, $vlan] = $this->salaParameters[$salaId];
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
     * Obtiene el estado actual de todas las salas
     */
    public function getSalasStatus(): array {
        $status = [];
        $vlans = [];
        $estados = $this->leerEstadoSala;

        // Inicializa todas las salas como desactivadas primero
        foreach (array_keys($this->salaParameters) as $salaId) {
            $status[$salaId] = false; // false = desactivada
            $vlans[$salaId] = '0';    // VLAN 0 = desactivada
        }

        // Para cada sala, consulta su estado mediante SNMP
        foreach ($this->salaParameters as $sala) {
            // Ejecuta el script leer_estado_salas pasando la IP del switch
            $process = new Process([$this->leerEstadoSala, $sala['ip']]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $output = explode("\n", $process->getOutput());
            // Procesa la salida del script para encontrar el puerto específico
            foreach ($output as $line) {
                // El formato de salida es algo como "identificador_puerto vlan"
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) >= 2) {
                    $currentVlan = trim($parts[1]);
                    // Si coincide con la VLAN esperada para esta sala
                    if ($currentVlan == $expectedVlan) {
                        $status[$salaId] = true;
                        $vlans[$salaId] = $currentVlan;
                    }
                }
            }
        }

        return ['status' => $status, 'vlans' => $vlans];
    }
}
