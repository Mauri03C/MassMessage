<!-- Agregar antes del cierre del nav -->
<div class="dropdown">
    <button class="btn btn-link position-relative" type="button" id="notificationsDropdown" 
            data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              id="notificationCount"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown"
         style="width: 300px; max-height: 400px; overflow-y: auto;">
        <div class="list-group list-group-flush" id="notificationsList">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregar antes del cierre del body -->
<script>
function loadNotifications() {
    fetch('/report/getNotifications')
        .then(response => response.json())
        .then(notifications => {
            const list = document.getElementById('notificationsList');
            list.innerHTML = '';

            if (notifications.length === 0) {
                list.innerHTML = '<div class="text-center p-3">No notifications</div>';
                return;
            }

            notifications.forEach(notification => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = `list-group-item list-group-item-action ${
                    notification.read_at ? '' : 'bg-light'
                }`;
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <small>${new Date(notification.created_at).toLocaleString()}</small>
                        ${!notification.read_at ? '<span class="badge bg-primary">New</span>' : ''}
                    </div>
                    <p class="mb-1">${notification.message}</p>
                `;
                item.onclick = (e) => {
                    e.preventDefault();
                    markNotificationRead(notification.id);
                };
                list.appendChild(item);
            });

            updateNotificationCount();
        });
}

function markNotificationRead(id) {
    fetch(`/report/markNotificationRead/${id}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadNotifications();
            }
        });
}

function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('#notificationsList .bg-light').length;
    const badge = document.getElementById('notificationCount');
    badge.textContent = unreadCount;
    badge.style.display = unreadCount > 0 ? 'block' : 'none';
}

// Cargar notificaciones inicialmente
loadNotifications();

// Actualizar cada minuto
setInterval(loadNotifications, 60000);
</script>