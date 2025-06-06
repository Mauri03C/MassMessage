<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-4">
    <h1 class="mb-4">Detailed Message Analysis</h1>

    <!-- Delivery Time Analysis -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Delivery Time Analysis</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Average Time</th>
                            <th>Minimum Time</th>
                            <th>Maximum Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['delivery_time'] as $analysis): ?>
                        <tr>
                            <td><?= ucfirst($analysis->type) ?></td>
                            <td><?= round($analysis->avg_delivery_time / 60, 2) ?> min</td>
                            <td><?= round($analysis->min_delivery_time / 60, 2) ?> min</td>
                            <td><?= round($analysis->max_delivery_time / 60, 2) ?> min</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Peak Sending Times Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Peak Sending Times</h5>
            <canvas id="peakTimesChart"></canvas>
        </div>
    </div>

    <!-- Hourly Delivery Rate Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Hourly Delivery Success Rate</h5>
            <canvas id="deliveryRateChart"></canvas>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Export Analysis</h5>
            <form id="exportForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary me-2" onclick="exportPdf()">Export to PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Datos para los gráficos
const peakTimesData = <?= json_encode($data['peak_times']) ?>;
const hourlyRateData = <?= json_encode($data['hourly_rate']) ?>;

// Configurar gráfico de horas pico
const peakTimesChart = new Chart(
    document.getElementById('peakTimesChart'),
    {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [
                {
                    label: 'Email',
                    data: Array.from({length: 24}, (_, hour) => {
                        const entry = peakTimesData.find(d => d.hour === hour && d.type === 'email');
                        return entry ? entry.count : 0;
                    }),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)'
                },
                {
                    label: 'WhatsApp',
                    data: Array.from({length: 24}, (_, hour) => {
                        const entry = peakTimesData.find(d => d.hour === hour && d.type === 'whatsapp');
                        return entry ? entry.count : 0;
                    }),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                },
                {
                    label: 'SMS',
                    data: Array.from({length: 24}, (_, hour) => {
                        const entry = peakTimesData.find(d => d.hour === hour && d.type === 'sms');
                        return entry ? entry.count : 0;
                    }),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }
);

// Configurar gráfico de tasa de entrega por hora
const deliveryRateChart = new Chart(
    document.getElementById('deliveryRateChart'),
    {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [{
                label: 'Success Rate (%)',
                data: Array.from({length: 24}, (_, hour) => {
                    const entry = hourlyRateData.find(d => d.hour === hour);
                    if (!entry) return 0;
                    return (entry.delivered / entry.total_sent * 100).toFixed(1);
                }),
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

function exportPdf() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    window.location.href = `/report/generatePdf?start_date=${startDate}&end_date=${endDate}`;
}
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>