<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BashScriptService
{
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

    public function __construct(ParameterBagInterface $params)
    {
        // El script estará ubicado en la carpeta bin del proyecto
        $this->scriptPath = $params->get('kernel.project_dir') . '/bin/sala_script.sh';
    }

    /**
     * Ejecuta el script bash para activar/desactivar una sala
     */
    public function toggleSala(string $salaId): array
    {
        if (!isset($this->salaParameters[$salaId])) {
            throw new \InvalidArgumentException("Sala no válida: $salaId");
        }

        [$swPlanta, $vlan] = $this->salaParameters[$salaId];
        
        $process = new Process([
            $this->scriptPath,
            $swPlanta,
            $vlan
        ]);
        
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
     * En un entorno real, esto consultaría el estado actual mediante SNMP
     */
    public function getSalasStatus(): array
    {
        // En un entorno real, esto consultaría el estado mediante SNMP
        // Por ahora, simulamos que todas las salas están desactivadas inicialmente
        $status = [];
        
        foreach (array_keys($this->salaParameters) as $salaId) {
            $status[$salaId] = false; // false = desactivada
        }
        
        return $status;
    }
}