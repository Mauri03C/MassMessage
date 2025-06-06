<?php require APPROOT . '/views/templates/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-file-alt me-2"></i>
            <?php echo isset($data['id']) ? 'Editar Plantilla' : 'Nueva Plantilla'; ?>
        </h2>
        <a href="<?php echo BASEURL; ?>/templates" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-body">
            <form action="<?php echo isset($data['id']) ? BASEURL . '/templates/edit/' . $data['id'] : BASEURL . '/templates/create'; ?>" 
                  method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre de la Plantilla</label>
                    <input type="text" class="form-control <?php echo isset($data['errors']['name']) ? 'is-invalid' : ''; ?>" 
                           id="name" name="name" value="<?php echo $data['name']; ?>" required>
                    <?php if(isset($data['errors']['name'])) : ?>
                        <div class="invalid-feedback"><?php echo $data['errors']['name']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Mensaje</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="type" id="type-email" 
                               value="email" <?php echo $data['type'] === 'email' ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-primary" for="type-email">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>

                        <input type="radio" class="btn-check" name="type" id="type-whatsapp" 
                               value="whatsapp" <?php echo $data['type'] === 'whatsapp' ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-primary" for="type-whatsapp">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </label>

                        <input type="radio" class="btn-check" name="type" id="type-sms" 
                               value="sms" <?php echo $data['type'] === 'sms' ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-primary" for="type-sms">
                            <i class="fas fa-sms me-2"></i>SMS
                        </label>
                    </div>
                    <?php if(isset($data['errors']['type'])) : ?>
                        <div class="invalid-feedback d-block"><?php echo $data['errors']['type']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Asunto</label>
                    <input type="text" class="form-control <?php echo isset($data['errors']['subject']) ? 'is-invalid' : ''; ?>" 
                           id="subject" name="subject" value="<?php echo $data['subject']; ?>" required>
                    <?php if(isset($data['errors']['subject'])) : ?>
                        <div class="invalid-feedback"><?php echo $data['errors']['subject']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenido</label>
                    <textarea class="form-control <?php echo isset($data['errors']['content']) ? 'is-invalid' : ''; ?>" 
                              id="content" name="content" rows="5" required><?php echo $data['content']; ?></textarea>
                    <?php if(isset($data['errors']['content'])) : ?>
                        <div class="invalid-feedback"><?php echo $data['errors']['content']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">
                        Puedes usar las siguientes variables en tu plantilla:
                        <ul class="mb-0">
                            <li>{nombre} - Nombre del destinatario</li>
                            <li>{fecha} - Fecha actual</li>
                            <li>{empresa} - Nombre de tu empresa</li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save me-2"></i>
                    <?php echo isset($data['id']) ? 'Actualizar Plantilla' : 'Guardar Plantilla'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/templates/footer.php'; ?>