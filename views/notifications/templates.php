<div class="container-fluid py-4">
    <h2>Plantillas de Notificación</h2>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Crear Nueva Plantilla</h5>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?controller=notification&action=createTemplate">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Plantilla</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Asunto del Email</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contenido del Email</label>
                    <textarea name="email_body" class="form-control" rows="5" required></textarea>
                    <small class="text-muted">Variables disponibles: {action}, {username}, {datetime}, {details}</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contenido de Notificación Web</label>
                    <textarea name="web_body" class="form-control" rows="3" required></textarea>
                    <small class="text-muted">Variables disponibles: {action}, {username}, {datetime}, {details}</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Crear Plantilla</button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Mis Plantillas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Asunto</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><?= htmlspecialchars($template['name']) ?></td>
                                <td><?= htmlspecialchars($template['subject']) ?></td>
                                <td><?= $template['created_at'] ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="editTemplate(<?= $template['id'] ?>)">Editar</button>
                                    <form method="post" action="index.php?controller=notification&action=deleteTemplate" style="display: inline;">
                                        <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
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
function editTemplate(id) {
    window.location.href = `index.php?controller=notification&action=editTemplate&id=${id}`;
}
</script>