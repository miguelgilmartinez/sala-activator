document.addEventListener('DOMContentLoaded', function () {
    const refreshIndicator = document.getElementById('refresh-indicator');
    refreshIndicator.style.display = 'none';
    // Manejador para el clic en las salas
    document.querySelectorAll('.departamento-select').forEach(salaElement => {
        salaElement.addEventListener('change', function () {
            const salaId = this.dataset.salaId;
            toggleSala(salaId);
        });
    });
    function toggleSala(salaId) {
        // Obtener el valor seleccionado del desplegable
        const selectedValue = this.querySelector('.departamento-select').value;
        addLogMessage(`Procesando sala ${salaId}...`);
        fetch('/toggle-sala', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sala: salaId,
                departamento: selectedValue // Valor del desplegable
            })
        })
        // ... (resto del código)
    }

// Función para actualizar el estado de todas las salas
    function updateSalasStatus() {
        refreshIndicator.style.display = 'block';
        fetch('/get-salas-status')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        addLogMessage(`Error: ${data.error}`, 'error');
                        return;
                    }

                    // Actualizar estados
                    for (const [salaId, status] of Object.entries(data)) {
                        updateSalaUI(salaId, status);
                    }

                    refreshIndicator.style.display = 'none';
                    addLogMessage('Estados de sala actualizados');
                })
                .catch(error => {
                    refreshIndicator.style.display = 'none';
                    addLogMessage(`Error al actualizar estados: ${error.message}`, 'error');
                });
    }

// Actualizar el UI de una sala
    function updateSalaUI(salaId, status) {
        const salaElement = document.querySelector(`[data-sala-id="${salaId}"]`);
        if (!salaElement)
            return;
        if (status) {
            salaElement.classList.remove('sala-inactive');
            salaElement.classList.add('sala-active');
        } else {
            salaElement.classList.remove('sala-active');
            salaElement.classList.add('sala-inactive');
        }

// También actualizar el borde según VLAN
// Aquí necesitamos el valor de VLAN que vendría en una respuesta extendida
// Por ahora solo actualizamos el estado activo/inactivo
    }

// Añadir mensaje al log
    function addLogMessage(message, type = 'info') {
        const logElement = document.getElementById('status-log');
        const timestamp = new Date().toLocaleTimeString();
        const messageElement = document.createElement('div');
        messageElement.className = `log-entry log-${type}`;
        messageElement.textContent = `[${timestamp}] ${message}`;
        logElement.appendChild(messageElement);
        logElement.scrollTop = logElement.scrollHeight;
    }

// Configurar actualización periódica (cada minuto)
    setInterval(updateSalasStatus, 60000);
    // Actualizar estados al cargar la página
    addLogMessage('Inicializando control de salas...');
    setTimeout(updateSalasStatus, 1000);
});
