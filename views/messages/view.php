<?php require APPROOT . '/views/templates/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope-open me-2"></i>Detalles del Mensaje</h2>
        <a href="<?php echo BASEURL; ?>/messages" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <?php flash('message_success'); ?>

    <div class="card mb-4" data-aos="fade-up">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h4 class="card-title"><?php echo $data['message']->subject; ?></h4>
                <span class="badge bg-<?php echo getStatusBadgeClass($data['message']->status); ?>">
                    <?php echo ucfirst($data['message']->status); ?>
                </span>
            </div>

            <div class="mb-4">
                <h6 class="text-muted mb-2">Contenido del Mensaje:</h6>
                <p class="card-text"><?php echo nl2br($data['message']->content); ?></p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="mb-0"><?php echo $data['message']->recipient_count; ?></h5>
                            <p class="text-muted mb-0">Destinatarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="mb-0"><?php echo $data['message']->sent_count; ?></h5>
                            <p class="text-muted mb-0">Enviados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h5 class="mb-0"><?php echo $data['message']->failed_count; ?></h5>
                            <p class="text-muted mb-0">Fallidos</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Destinatario</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha de Envío</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['recipients'] as $recipient) : ?>
                            <tr>
                                <td><?php echo $recipient->contact; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getTypeClass($recipient->type); ?>">
                                        <?php echo ucfirst($recipient->type); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeClass($recipient->status); ?>">
                                        <?php echo ucfirst($recipient->status); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($recipient->sent_at)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/templates/footer.php'; ?>