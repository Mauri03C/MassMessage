<?php 
// $data['active_nav'] y $data['page_title'] son pasadas desde MessagesController::index()
// $data['messages'] también es pasada (actualmente como array vacío)
require_once APPROOT . '/views/includes/header.php'; 
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history me-2"></i><?php echo htmlspecialchars($data['page_title'] ?? 'Historial de Mensajes'); ?></h1>
        <a href="<?php echo BASEURL; ?>/messages/create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Crear Nuevo Mensaje
        </a>
    </div>

    <?php // Aquí podrías poner mensajes flash, por ejemplo, si se borra un mensaje ?>
    <?php // flash('message_action_status'); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mensajes Enviados y Programados</h6>
        </div>
        <div class="card-body">
            <?php if (empty($data['messages'])) : ?>
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    No tienes mensajes en tu historial todavía. <a href="<?php echo BASEURL; ?>/messages/create" class="alert-link">¡Crea tu primer mensaje!</a>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableMessages" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Título / Asunto</th>
                                <th>Destinatarios (Grupo)</th>
                                <th>Canal</th>
                                <th>Fecha de Envío / Programación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['messages'] as $message) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($message->id); ?></td>
                                    <td><?php echo htmlspecialchars($message->title ?? $message->subject ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($message->recipient_group_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $channel_icon = 'fas fa-question-circle';
                                            if ($message->channel == 'sms') $channel_icon = 'fas fa-sms';
                                            if ($message->channel == 'whatsapp') $channel_icon = 'fab fa-whatsapp';
                                            if ($message->channel == 'email') $channel_icon = 'fas fa-envelope';
                                        ?>
                                        <i class="<?php echo $channel_icon; ?> me-1"></i> <?php echo ucfirst(htmlspecialchars($message->channel)); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($message->send_time ?? $message->created_at))); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo htmlspecialchars($message->status_class ?? 'secondary'); ?>">
                                            <?php echo htmlspecialchars($message->status_text ?? 'Desconocido'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASEURL; ?>/messages/show/<?php echo $message->id; ?>" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                                        <?php // Podrías añadir botones para editar/eliminar si el mensaje no ha sido enviado ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>