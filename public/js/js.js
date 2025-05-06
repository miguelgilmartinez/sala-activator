document.addEventListener('DOMContentLoaded', function () {

    const refreshIndicator = document.getElementById('refresh-indicator');
    refreshIndicator.style.display = 'none';
    // Manejador para el clic en las salas
    document.querySelectorAll('.departamento-select').forEach(salaElement => {
        salaElement.addEventListener('change', function () {
//            const salaId = this[this.selectedIndex].value;
            this.classList.remove('IEM', 'Hacienda', 'Fomento');
            this.classList.add(this.options[this.selectedIndex].text);
            toggleSala(this.dataset.salaId, this.dataset.salaNombre);

        });
    });

    function toggleSala(salaId, salaNombre) {
        // Obtener el valor seleccionado del desplegable
        //  const selectedValue = this.querySelector('.departamento-select').value;
        addLogMessage(`Activando VLAN sala ${salaNombre}`);
        fetch('/toggle-sala', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sala: salaId,
                config: '10 11 12' // Valor del desplegable
            })
        })

    }

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
            
        }

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
    addLogMessage('Inicializando control de salas');
    setTimeout(updateSalasStatus, 1000);
});
