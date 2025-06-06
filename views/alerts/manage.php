<div class="container-fluid py-4">
    <h2>Gestión de Alertas</h2>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Crear Nueva Alerta</h5>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?controller=alert&action=create">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Actividad</label>
                        <select name="type" class="form-select" required>
                            <option value="message_sent">Mensaje Enviado</option>
                            <option value="template_created">Plantilla Creada</option>
                            <option value="contact_imported">Contacto Importado</option>
                            <option value="login">Inicio de Sesión</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Frecuencia (en 1 hora)</label>
                        <input type="number" name="frequency" class="form-control" min="1" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Método de Notificación</label>
                        <select name="notification_method" class="form-select" required>
                            <option value="email">Email</option>
                            <option value="web">Notificación Web</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Hora de Inicio</label>
                        <input type="time" name="time_window_start" class="form-control">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Hora de Fin</label>
                        <input type="time" name="time_window_end" class="form-control">
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Crear Alerta</button>
                </div>
            </form>
        <!-- Agregar después del formulario de creación de alertas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sugerencias Basadas en Estadísticas</h5>
            </div>
            <div class="card-body">
                <?php $suggestions = $alertModel->suggestAlerts($userId); ?>
                
                <div class="accordion" id="suggestionsAccordion">
                    <?php foreach ($suggestions as $action => $data): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= ucfirst($action) ?>">
                                Sugerencias para <?= ucfirst(str_replace('_', ' ', $action)) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= ucfirst($action) ?>" class="accordion-collapse collapse" data-bs-parent="#suggestionsAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Frecuencias Típicas:</h6>
                                        <ul>
                                            <li>Normal: <?= $data['normal_frequency'] ?> por hora</li>
                                            <li>Alta: <?= $data['high_frequency'] ?> por hora</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Períodos de Actividad:</h6>
                                        <ul>
                                            <li>Horas Pico: <?= sprintf("%02d:00-%02d:00", $data['recommended_window']['start'], $data['recommended_window']['end']) ?></li>
                                            <li>Horas Tranquilas: <?= sprintf("%02d:00-%02d:00", $data['quiet_period']['start'], $data['quiet_period']['end']) ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-primary btn-sm" onclick="applyAlertSuggestion('<?= $action ?>', <?= json_encode($data) ?>)">
                                        Aplicar Sugerencia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Mis Alertas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Condiciones</th>
                            <th>Método de Notificación</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                            <tr>
                                <td><?= htmlspecialchars($alert['type']) ?></td>
                                <td>
                                    <?php 
                                    $conditions = json_decode($alert['conditions'], true);
                                    foreach ($conditions as $key => $value) {
                                        echo htmlspecialchars("$key: $value") . "<br>";
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($alert['notification_method']) ?></td>
                                <td><?= $alert['created_at'] ?></td>
                                <td>
                                    <form method="post" action="index.php?controller=alert&action=delete" style="display: inline;">
                                        <input type="hidden" name="alert_id" value="<?= $alert['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function applyAlertSuggestion(action, data) {
    document.querySelector('select[name="type"]').value = action;
    document.querySelector('input[name="frequency"]').value = data.high_frequency;
    document.querySelector('input[name="time_window_start"]').value = 
        `${String(data.recommended_window.start).padStart(2, '0')}:00`;
    document.querySelector('input[name="time_window_end"]').value = 
        `${String(data.recommended_window.end).padStart(2, '0')}:00`;
}
</script>