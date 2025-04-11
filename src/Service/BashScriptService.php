<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BashScriptService {

    private string $scriptPath;
    // Mapeo de salas a parámetros (swPlanta y vlan)
    private array $salaParameters = [
        '1' => ['10.0.0.1', '101'],
        '2' => ['10.0.0.2', '102'],
        '3' => ['10.0.0.3', '103'],
        '4' => ['10.0.0.4', '104'],
        '6' => ['10.0.0.6', '106'],
        'Palomar' => ['10.0.0.10', '110'],
    ];

    public function __construct(ParameterBagInterface $params) {
        // El script estará ubicado en la carpeta bin del proyecto
        $this->scriptPath = $params->get('kernel.project_dir') . '/bin/fijar_estado_sala.sh';
    }

    /**
     * Ejecuta el script bash para activar/desactivar una sala
     */
    public function toggleSala(string $salaId): array {
        if (!isset($this->salaParameters[$salaId])) {
            throw new \InvalidArgumentException("Sala no válida: $salaId");
        }

        [$swPlanta, $vlan] = $this->salaParameters[$salaId];

        $process = new Process([$this->scriptPath, $swPlanta, $vlan]);
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

        // Inicializa todas las salas como desactivadas primero
        foreach (array_keys($this->salaParameters) as $salaId) {
            $status[$salaId] = false; // false = desactivada
        }

        // Para cada sala, consulta su estado mediante SNMP
        foreach ($this->salaParameters as $salaId => [$switchIp, $puerto]) {
            // Ejecuta el script leer_estado_salas pasando la IP del switch
            $command = '/bin/leer_estado_salas ' . escapeshellarg($switchIp);
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                // Manejo de errores si el script falla
                continue;
            }

            // Procesa la salida del script para encontrar el puerto específico
            foreach ($output as $line) {
                // Busca la línea que contiene la información del puerto de esta sala
                if (strpos($line, $puerto) !== false) {
                    // El formato de salida es algo como "identificador_puerto vlan"
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 2) {
                        $vlan = (int) $parts[1];
                        // Determina si la sala está activa según la VLAN
                        // Suponiendo que VLAN > 0 indica que está activa
                        $status[$salaId] = ($vlan > 0);
                        // Encontramos el puerto, salimos del bucle
                        break;
                    }
                }
            }
        }
        return $status;
    }
}
