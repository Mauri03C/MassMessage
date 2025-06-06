<?php 
// $data['active_nav'] es pasada desde MessagesController::create()
require_once APPROOT . '/views/includes/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0"><i class="fas fa-plus-circle me-2"></i><?php echo htmlspecialchars($data['page_title'] ?? 'Crear Mensaje'); ?></h1>
                </div>
                <div class="card-body">
                    <p class="text-muted">Completa el formulario para redactar y programar tu mensaje masivo.</p>
                    
                    <form action="<?php echo BASEURL; ?>/messages/store" method="post">
                        <?php // Aquí podrías incluir un campo CSRF si lo implementas ?>
                        <!-- <input type="hidden" name="csrf_token" value="<?php // echo createCSRFToken(); ?>"> -->

                        <div class="mb-3">
                            <label for="message_title" class="form-label">Título del Mensaje (interno):</label>
                            <input type="text" class="form-control" id="message_title" name="message_title" placeholder="Ej: Campaña de Verano" required>
                            <div class="form-text">Este título es para tu referencia y no se muestra a los destinatarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="message_content" class="form-label">Contenido del Mensaje:</label>
                            <textarea class="form-control" id="message_content" name="message_content" rows="5" placeholder="Escribe aquí tu mensaje..." required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="recipient_group" class="form-label">Grupo de Destinatarios:</label>
                                <select class="form-select" id="recipient_group" name="recipient_group">
                                    <option selected disabled value="">Selecciona un grupo...</option>
                                    <!-- Aquí cargarías los grupos desde la BD -->
                                    <option value="1">Clientes VIP</option>
                                    <option value="2">Suscriptores Newsletter</option>
                                    <option value="3">Todos los Contactos</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="send_time" class="form-label">Programar Envío (opcional):</label>
                                <input type="datetime-local" class="form-control" id="send_time" name="send_time">
                                <div class="form-text">Si se deja en blanco, se intentará enviar inmediatamente.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Canal de Envío:</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_sms" value="sms" checked>
                                    <label class="form-check-label" for="channel_sms">SMS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_whatsapp" value="whatsapp">
                                    <label class="form-check-label" for="channel_whatsapp">WhatsApp</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_email" value="email" disabled>
                                    <label class="form-check-label" for="channel_email">Email (próximamente)</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?php echo BASEURL; ?>/dashboard" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>