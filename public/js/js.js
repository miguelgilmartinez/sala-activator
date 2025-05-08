document.addEventListener('DOMContentLoaded', function () {

    const refreshIndicator = document.getElementById('refresh-indicator');
    refreshIndicator.style.display = 'none';
    // Manejador para el clic en las salas
    document.querySelectorAll('.departamento-select').forEach(salaElement => {
        salaElement.addEventListener('change', function () {
            // const salaId = this[this.selectedIndex].value;
            this.classList.remove('IEM', 'Hacienda', 'Fomento');
            this.classList.add(this.options[this.selectedIndex].text);
            toggleSala(this.dataset.salaId, this.dataset.salaNombre, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text);
        });
    });

    function toggleSala(salaId, salaNombre, vlan, consejeria) {
        console.log(salaNombre, '->', vlan);
        // Obtener el valor seleccionado del desplegable
        //  const selectedValue = this.querySelector('.departamento-select').value;
        addLogMessage(`Activando VLAN ${vlan} (${consejeria}) en ${salaNombre}`);
        fetch('/toggle-sala', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                salaId: salaId,
                vlan: vlan
            })
        }).then(response => response.json())
                .then(data => {
                    if (data.error) {
                        addLogMessage(`Error: ${data.error}`, 'error');
                        return;
                    }
                    console.log(data);
                    addLogMessage(`Sala ${data.sala} modificada a vlan ${data.vlan}`);
                })
                .catch(error => {
                    refreshIndicator.style.display = 'none';
                    addLogMessage(`Error al modificar vlan: ${error.message}`, 'error');
                });
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
        // Buscamos la opción cuyo value empieza por el número indicado en el JSON
        Array.from(salaElement.options).find(option => {
            const primerValor = parseInt(option.value.split(' ')[0]); // Primer número del value
            if (primerValor == status) {
                option.setAttribute('selected', 'selected'); // Marca como seleccionado
                salaElement.value = option.value; // Asignamos el valor al select
                salaElement.classList.remove('IEM', 'Hacienda', 'Fomento');
                salaElement.classList.add(salaElement.options[salaElement.selectedIndex].text);
            }
        });
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
