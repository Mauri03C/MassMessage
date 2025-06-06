document.addEventListener('DOMContentLoaded', function() {
    // Manejar guardado de plantilla
    document.getElementById('saveTemplate').addEventListener('click', function() {
        const form = document.getElementById('templateForm');
        const formData = new FormData(form);
        
        fetch('/templates/save', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Plantilla guardada exitosamente', 'success');
                location.reload();
            } else {
                showNotification('Error al guardar la plantilla', 'error');
            }
        })
        .catch(error => {
            showNotification('Error al guardar la plantilla', 'error');
        });
    });

    // Manejar edición de plantilla
    document.querySelectorAll('.edit-template').forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.dataset.id;
            
            fetch(`/templates/get/${templateId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('templateId').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('subject').value = data.subject;
                    document.getElementById('content').value = data.content;
                    document.getElementById('variables').value = 
                        JSON.parse(data.variables).join(', ');
                    
                    new bootstrap.Modal(document.getElementById('templateModal')).show();
                });
        });
    });

    // Manejar eliminación de plantilla
    document.querySelectorAll('.delete-template').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Está seguro de que desea eliminar esta plantilla?')) {
                const templateId = this.dataset.id;
                
                fetch(`/templates/delete/${templateId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Plantilla eliminada exitosamente', 'success');
                        location.reload();
                    } else {
                        showNotification('Error al eliminar la plantilla', 'error');
                    }
                });
            }
        });
    });
});

function showNotification(message, type) {
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: 'right',
        backgroundColor: type === 'success' ? '#28a745' : '#dc3545'
    }).showToast();
}