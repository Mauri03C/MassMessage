<div class="container-fluid py-4">
    <h2>Estadísticas de Alertas</h2>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Alertas</h5>
                    <h2 class="mb-0"><?= $stats['total_alerts'] ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Alertas Activadas Hoy</h5>
                    <h2 class="mb-0"><?= $stats['alerts_today'] ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Notificaciones Enviadas</h5>
                    <h2 class="mb-0"><?= $stats['notifications_sent'] ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tasa de Éxito</h5>
                    <h2 class="mb-0"><?= number_format($stats['success_rate'], 1) ?>%</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Alertas por Tipo</h5>
                </div>
                <div class="card-body">
                    <canvas id="alertsByTypeChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Alertas por Método de Notificación</h5>
                </div>
                <div class="card-body">
                    <canvas id="alertsByMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Historial de Alertas</h5>
        </div>
        <div class="card-body">
            <canvas id="alertHistoryChart"></canvas>
        </div>
    </div>
</div>

<script>
// Gráfico de Alertas por Tipo
const typeCtx = document.getElementById('alertsByTypeChart').getContext('2d');
const typeData = <?= json_encode($stats['alerts_by_type']) ?>;
new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(typeData),
        datasets: [{
            data: Object.values(typeData),
            backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#F44336']
        }]
    }
});

// Gráfico de Alertas por Método
const methodCtx = document.getElementById('alertsByMethodChart').getContext('2d');
const methodData = <?= json_encode($stats['alerts_by_method']) ?>;
new Chart(methodCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(methodData),
        datasets: [{
            data: Object.values(methodData),
            backgroundColor: ['#2196F3', '#4CAF50']
        }]
    }
});

// Gráfico de Historial de Alertas
const historyCtx = document.getElementById('alertHistoryChart').getContext('2d');
const historyData = <?= json_encode($stats['alert_history']) ?>;
new Chart(historyCtx, {
    type: 'line',
    data: {
        labels: historyData.map(d => d.date),
        datasets: [{
            label: 'Alertas Activadas',
            data: historyData.map(d => d.count),
            borderColor: '#2196F3',
            fill: false
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<!-- Agregar después de las tarjetas de estadísticas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detección de Anomalías</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tipo de Actividad</th>
                                <th>Frecuencia Normal</th>
                                <th>Frecuencia Actual</th>
                                <th>Estado</th>
                                <th>Recomendación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anomalies as $action => $data): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $action)) ?></td>
                                    <td><?= $data['normal_range'] ?> por hora</td>
                                    <td><?= $data['current_frequency'] ?> por hora</td>
                                    <td>
                                        <?php if ($data['status'] === 'normal'): ?>
                                            <span class="badge bg-success">Normal</span>
                                        <?php elseif ($data['status'] === 'high'): ?>
                                            <span class="badge bg-warning">Alto</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Anómalo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $data['recommendation'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>