class MessageIntegration {
    constructor() {
        this.form = document.getElementById('message-form');
        this.analysisBtn = document.getElementById('analyze-btn');
        this.translateBtn = document.getElementById('translate-btn');
        this.sendBtn = document.getElementById('send-btn');
        this.resultsDiv = document.getElementById('results');

        this.setupEventListeners();
    }

    setupEventListeners() {
        this.analysisBtn.addEventListener('click', () => this.analyzeMessage());
        this.translateBtn.addEventListener('click', () => this.translateMessage());
        this.sendBtn.addEventListener('click', () => this.sendMessages());
    }

    async analyzeMessage() {
        const content = document.getElementById('message-content').value;
        try {
            const response = await fetch('/integration/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content })
            });

            const data = await response.json();
            this.displayAnalysis(data.analysis);
        } catch (error) {
            this.showError('Error en el análisis: ' + error.message);
        }
    }

    async translateMessage() {
        const content = document.getElementById('message-content').value;
        const targetLang = document.getElementById('target-lang').value;

        try {
            const response = await fetch('/integration/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content, target_lang: targetLang })
            });

            const data = await response.json();
            this.displayTranslation(data.translation);
        } catch (error) {
            this.showError('Error en la traducción: ' + error.message);
        }
    }

    async sendMessages() {
        const messages = this.getSelectedMessages();
        const channel = document.getElementById('channel').value;

        try {
            const response = await fetch('/integration/send-bulk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ messages, channel })
            });

            const data = await response.json();
            this.displayResults(data.results);
        } catch (error) {
            this.showError('Error en el envío: ' + error.message);
        }
    }

    displayAnalysis(analysis) {
        this.resultsDiv.innerHTML = `
            <div class="analysis-results">
                <h3>Análisis de Contenido</h3>
                <div class="sentiment">${analysis.sentiment}</div>
                <div class="suggestions">
                    <h4>Sugerencias:</h4>
                    <ul>
                        ${analysis.suggestions.map(s => `<li>${s}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    displayTranslation(translation) {
        document.getElementById('message-content').value = translation.text;
        this.showSuccess('Traducción completada');
    }

    displayResults(results) {
        this.resultsDiv.innerHTML = `
            <div class="send-results">
                <h3>Resultados del Envío</h3>
                <div class="stats">
                    <div>Enviados: ${results.filter(r => !r.error).length}</div>
                    <div>Fallidos: ${results.filter(r => r.error).length}</div>
                </div>
                ${results.some(r => r.error) ? this.displayErrors(results) : ''}
            </div>
        `;
    }

    displayErrors(results) {
        return `
            <div class="errors">
                <h4>Errores:</h4>
                <ul>
                    ${results
                        .filter(r => r.error)
                        .map(r => `<li>${r.error}</li>`)
                        .join('')}
                </ul>
            </div>
        `;
    }

    getSelectedMessages() {
        // Implementar lógica para obtener mensajes seleccionados
        return [];
    }

    showError(message) {
        this.resultsDiv.innerHTML = `<div class="error">${message}</div>`;
    }

    showSuccess(message) {
        this.resultsDiv.innerHTML = `<div class="success">${message}</div>`;
    }
}

new MessageIntegration();