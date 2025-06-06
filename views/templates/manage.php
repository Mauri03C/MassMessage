<?php require_once 'views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Plantillas de Notificación</h2>
        <?php if (hasPermission('create_templates')): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModal">
            <i class="fas fa-plus"></i> Nueva Plantilla
        </button>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Asunto</th>
                            <th>Variables</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                        <tr>
                            <td><?= htmlspecialchars($template['name']) ?></td>
                            <td><?= htmlspecialchars($template['subject']) ?></td>
                            <td>
                                <?php 
                                $variables = json_decode($template['variables'], true);
                                echo implode(', ', array_map(function($var) {
                                    return '{{' . $var . '}}';
                                }, $variables));
                                ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($template['created_at'])) ?></td>
                            <td>
                                <?php if (hasPermission('edit_templates')): ?>
                                <button class="btn btn-sm btn-info edit-template" 
                                        data-id="<?= $template['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (hasPermission('delete_templates')): ?>
                                <button class="btn btn-sm btn-danger delete-template"
                                        data-id="<?= $template['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar plantillas -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plantilla de Notificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="templateForm">
                    <input type="hidden" name="id" id="templateId">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="variables" class="form-label">Variables (separadas por coma)</label>
                        <input type="text" class="form-control" id="variables" name="variables"
                               placeholder="nombre, email, fecha">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveTemplate">Guardar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/includes/footer.php'; ?>