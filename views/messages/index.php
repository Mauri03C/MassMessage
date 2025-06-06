<?php require APPROOT . '/views/templates/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope me-2"></i>Mis Mensajes</h2>
        <a href="<?php echo BASEURL; ?>/messages/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Mensaje
        </a>
    </div>

    <?php flash('message_success'); ?>

    <div class="row">
        <?php foreach($data['messages'] as $message) : ?>
            <div class="col-md-6 mb-4" data-aos="fade-up">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title"><?php echo $message->subject; ?></h5>
                            <span class="badge bg-<?php echo getStatusBadgeClass($message->status); ?>">
                                <?php echo ucfirst($message->status); ?>
                            </span>
                        </div>

                        <p class="card-text"><?php echo substr($message->content, 0, 100) . '...'; ?></p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($message->created_at)); ?>
                            </div>
                            <div>
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-user me-1"></i><?php echo $message->recipient_count; ?>
                                </span>
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check me-1"></i><?php echo $message->sent_count; ?>
                                </span>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i><?php echo $message->failed_count; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo BASEURL; ?>/messages/view/<?php echo $message->id; ?>" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye me-2"></i>Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($data['messages'])) : ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No hay mensajes</h4>
            <p>Comienza creando un nuevo mensaje</p>
            <a href="<?php echo BASEURL; ?>/messages/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Mensaje
            </a>
        </div>
    <?php endif; ?>

    <!-- Paginación -->
    <?php if(!empty($data['messages'])) : ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= ceil(count($data['messages']) / 10); $i++) : ?>
                    <li class="page-item <?php echo $data['current_page'] == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo BASEURL; ?>/messages?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/templates/footer.php'; ?>