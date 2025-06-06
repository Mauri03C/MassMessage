// Inicialización de Google Auth
function initGoogleAuth() {
    gapi.load('auth2', function() {
        gapi.auth2.init({
            client_id: GOOGLE_CLIENT_ID
        });
    });
}

// Función para iniciar sesión con Google
function loginWithGoogle() {
    const auth2 = gapi.auth2.getAuthInstance();
    auth2.signIn().then(function(googleUser) {
        const profile = googleUser.getBasicProfile();
        const id_token = googleUser.getAuthResponse().id_token;

        // Enviar token al servidor
        fetch(BASEURL + '/auth/googleAuth', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                token: id_token,
                email: profile.getEmail(),
                name: profile.getName()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = BASEURL + '/home';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo iniciar sesión con Google'
                });
            }
        });
    });
}

// Mostrar mensajes de error o éxito
function showAlert(type, message) {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'Éxito' : 'Error',
        text: message,
        timer: 3000,
        timerProgressBar: true
    });
}

// Animaciones para elementos que aparecen
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out'
    });
});

// Variables de plantilla
const templateVariables = {
    nombre: 'Nombre del destinatario',
    fecha: new Date().toLocaleDateString(),
    empresa: 'Nombre de la empresa'
};

function replaceTemplateVariables(text) {
    return text.replace(/{([^}]+)}/g, (match, variable) => {
        return templateVariables[variable] || match;
    });
}

// Actualizar el manejo de plantillas
templateItems.forEach(item => {
    item.addEventListener('click', () => {
        const type = item.dataset.type;
        const typeRadio = document.querySelector(`#type-${type}`);
        if (typeRadio) {
            typeRadio.checked = true;
        }

        subjectInput.value = replaceTemplateVariables(item.dataset.subject);
        contentInput.value = replaceTemplateVariables(item.dataset.content);

        // Mostrar notificación
        showToast('success', 'Plantilla aplicada correctamente');
    });
});

// Función para mostrar notificaciones
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