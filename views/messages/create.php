<?php require APPROOT . '/views/templates/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-paper-plane me-2"></i>Nuevo Mensaje</h2>
        <a href="<?php echo BASEURL; ?>/messages" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <?php flash('message_error'); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-body">
                    <form action="<?php echo BASEURL; ?>/messages/create" method="POST">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Asunto</label>
                            <input type="text" class="form-control <?php echo isset($data['errors']['subject']) ? 'is-invalid' : ''; ?>" 
                                   id="subject" name="subject" value="<?php echo $data['subject'] ?? ''; ?>" required>
                            <?php if(isset($data['errors']['subject'])) : ?>
                                <div class="invalid-feedback"><?php echo $data['errors']['subject']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido</label>
                            <textarea class="form-control <?php echo isset($data['errors']['content']) ? 'is-invalid' : ''; ?>" 
                                      id="content" name="content" rows="5" required><?php echo $data['content'] ?? ''; ?></textarea>
                            <?php if(isset($data['errors']['content'])) : ?>
                                <div class="invalid-feedback"><?php echo $data['errors']['content']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Mensaje</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="type-email" value="email" checked>
                                <label class="btn btn-outline-primary" for="type-email">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type-whatsapp" value="whatsapp">
                                <label class="btn btn-outline-primary" for="type-whatsapp">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type-sms" value="sms">
                                <label class="btn btn-outline-primary" for="type-sms">
                                    <i class="fas fa-sms me-2"></i>SMS
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destinatarios</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="recipient" placeholder="Email, teléfono o número de WhatsApp">
                                <button class="btn btn-outline-primary" type="button" id="addRecipient">
                                    <i class="fas fa-plus me-2"></i>Agregar
                                </button>
                            </div>
                            <div id="recipientList" class="mb-3"></div>
                            <input type="hidden" name="recipients" id="recipientsInput">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Importar Contactos</label>
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" id="contactFile" accept=".csv,.xlsx">
                                <button class="btn btn-outline-primary" type="button" id="importContacts">
                                    <i class="fas fa-file-import me-2"></i>Importar
                                </button>
                            </div>
                            <div class="form-text">Formatos soportados: CSV, Excel (XLSX)</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-3">Plantillas</h5>
                    <div class="list-group">
                        <?php foreach($data['templates'] as $template) : ?>
                            <button type="button" class="list-group-item list-group-item-action template-item"
                                    data-subject="<?php echo htmlspecialchars($template->subject); ?>"
                                    data-content="<?php echo htmlspecialchars($template->content); ?>"
                                    data-type="<?php echo $template->type; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?php echo $template->name; ?></h6>
                                    <span class="badge bg-<?php echo getTypeClass($template->type); ?>">
                                        <?php echo ucfirst($template->type); ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-truncate"><?php echo $template->subject; ?></p>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Recipient management
let recipients = [];
const recipientInput = document.getElementById('recipient');
const addRecipientBtn = document.getElementById('addRecipient');
const recipientList = document.getElementById('recipientList');
const recipientsInput = document.getElementById('recipientsInput');

function updateRecipientList() {
    recipientList.innerHTML = '';
    recipients.forEach((recipient, index) => {
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary me-2 mb-2';
        badge.innerHTML = `
            ${recipient}
            <button type="button" class="btn-close btn-close-white" 
                    aria-label="Close" onclick="removeRecipient(${index})"></button>
        `;
        recipientList.appendChild(badge);
    });
    recipientsInput.value = JSON.stringify(recipients);
}

function addRecipient() {
    const recipient = recipientInput.value.trim();
    if (recipient && !recipients.includes(recipient)) {
        recipients.push(recipient);
        updateRecipientList();
        recipientInput.value = '';
    }
}

function removeRecipient(index) {
    recipients.splice(index, 1);
    updateRecipientList();
}

addRecipientBtn.addEventListener('click', addRecipient);
recipientInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        addRecipient();
    }
});

// Contact import
const contactFile = document.getElementById('contactFile');
const importContactsBtn = document.getElementById('importContacts');

importContactsBtn.addEventListener('click', () => {
    const file = contactFile.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    fetch(`${BASEURL}/messages/import-contacts`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            data.contacts.forEach(contact => {
                if (!recipients.includes(contact)) {
                    recipients.push(contact);
                }
            });
            updateRecipientList();
            contactFile.value = '';
            showToast('success', 'Contactos importados correctamente');
        } else {
            showToast('error', data.message || 'Error al importar contactos');
        }
    })
    .catch(error => {
        showToast('error', 'Error al importar contactos');
    });
});

// Template management
const templateItems = document.querySelectorAll('.template-item');
const subjectInput = document.getElementById('subject');
const contentInput = document.getElementById('content');

templateItems.forEach(item => {
    item.addEventListener('click', () => {
        subjectInput.value = item.dataset.subject;
        contentInput.value = item.dataset.content;
    });
});

// Toast notification
function showToast(type, message) {
    Swal.fire({
        icon: type,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}
</script>

<?php require APPROOT . '/views/templates/footer.php'; ?>