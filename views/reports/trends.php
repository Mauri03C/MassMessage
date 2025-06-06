<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Trend Analysis & Predictions</h1>
        <select id="periodSelect" class="form-select" style="width: auto;">
            <option value="week">Last 7 Days</option>
            <option value="month" selected>Last 30 Days</option>
            <option value="year">Last Year</option>
        </select>
    </div>

    <!-- Predictions Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Delivery Rate Predictions</h5>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="display-4"><?= $data['predictions']['next_day']['expected_rate'] ?>%</h2>
                    <p class="text-muted">Expected delivery rate for tomorrow</p>
                </div>
                <div class="col-md-6">
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?= $data['predictions']['next_day']['confidence'] ?>%;"
                             aria-valuenow="<?= $data['predictions']['next_day']['confidence'] ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <?= $data['predictions']['next_day']['confidence'] ?>% Confidence
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Success Rate Trend -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Delivery Success Rate Trend</h5>
                    <canvas id="successRateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Volume Trend -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Message Volume Trend</h5>
                    <canvas id="volumeChart"></canvas>
                    <p class="mt-2 text-center">
                        Trend: <strong><?= ucfirst($data['trends']['volume_trend']['trend']) ?></strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Peak Hours -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Peak Sending Hours</h5>
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Message Type Distribution -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Message Type Distribution</h5>
                    <canvas id="typeDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para los gráficos
const trendsData = <?= json_encode($data['trends']) ?>;

// Configurar gráficos usando Chart.js
const successRateChart = new Chart(
    document.getElementById('successRateChart'),
    {
        type: 'line',
        data: {
            labels: trendsData.success_rate.map(d => d.date),
            datasets: [{
                label: 'Success Rate (%)',
                data: trendsData.success_rate.map(d => d.rate),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    }
);

// Implementar los demás gráficos de manera similar

// Manejar cambio de período
document.getElementById('periodSelect').addEventListener('change', (e) => {
    window.location.href = `/report/trends?period=${e.target.value}`;
});
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>