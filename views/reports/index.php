<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-4">
    <!-- Agregar después del div.container y antes de las Summary Cards -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Message Reports & Statistics</h1>
        <div>
            <a href="/report/detailedAnalysis" class="btn btn-primary me-2">View Detailed Analysis</a>
            <a href="/report/schedules" class="btn btn-secondary">Manage Report Schedules</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Messages</h5>
                    <h2 class="card-text"><?= $data['total_messages'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Delivery Rate</h5>
                    <h2 class="card-text">
                        <?php
                        $stats = $data['delivery_stats'];
                        $rate = $stats->total_recipients > 0 
                            ? round(($stats->delivered / $stats->total_recipients) * 100, 1)
                            : 0;
                        echo $rate . '%';
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Failed Deliveries</h5>
                    <h2 class="card-text"><?= $data['delivery_stats']->failed ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Controls -->
    <div class="row mb-4">
        <div class="col-md-6">
            <select id="periodSelect" class="form-select">
                <option value="week">Last 7 Days</option>
                <option value="month">Last 30 Days</option>
                <option value="year">Last Year</option>
            </select>
        </div>
        <div class="col-md-6">
            <select id="typeSelect" class="form-select">
                <option value="all">All Types</option>
                <option value="email">Email</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="sms">SMS</option>
            </select>
        </div>
    </div>

    <!-- Message Volume Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Message Volume</h5>
            <canvas id="messageChart"></canvas>
        </div>
    </div>

    <!-- Export Controls -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Export Report</h5>
            <form id="exportForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Message Type</label>
                    <select class="form-select" id="exportType" name="type">
                        <option value="all">All Types</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Export to CSV</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Messages Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Recent Messages</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Recipients</th>
                            <th>Delivered</th>
                            <th>Failed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['recent_messages'] as $message): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', strtotime($message->created_at)) ?></td>
                            <td><?= ucfirst($message->type) ?></td>
                            <td><?= htmlspecialchars($message->subject) ?></td>
                            <td><?= $message->total_recipients ?></td>
                            <td><?= $message->delivered ?></td>
                            <td><?= $message->failed ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const messageChart = new Chart(
    document.getElementById('messageChart'),
    {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Total Messages',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                {
                    label: 'Delivered',
                    data: [],
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
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

async function updateChart() {
    const period = document.getElementById('periodSelect').value;
    const type = document.getElementById('typeSelect').value;
    
    const response = await fetch(`/report/getChartData?period=${period}&type=${type}`);
    const data = await response.json();
    
    messageChart.data.labels = data.map(item => item.date);
    messageChart.data.datasets[0].data = data.map(item => item.total);
    messageChart.data.datasets[1].data = data.map(item => item.delivered);
    messageChart.update();
}

document.getElementById('periodSelect').addEventListener('change', updateChart);
document.getElementById('typeSelect').addEventListener('change', updateChart);

document.getElementById('exportForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const form = e.target;
    const params = new URLSearchParams(new FormData(form));
    window.location.href = `/report/exportCsv?${params.toString()}`;
});

// Initialize chart
updateChart();
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<!-- Agregar después del div.container y antes del contenido existente -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard</h1>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
            <i class="fas fa-plus"></i> Add Widget
        </button>
        <a href="/report/detailedAnalysis" class="btn btn-secondary ms-2">View Detailed Analysis</a>
    </div>
</div>

<!-- Widget Grid -->
<div class="row" id="widgetGrid">
    <?php foreach ($data['widgets'] as $widget): ?>
    <div class="col-md-6 col-lg-4 mb-4" data-widget-id="<?= $widget->id ?>">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?= htmlspecialchars($widget->title) ?></h5>
                <div class="dropdown">
                    <button class="btn btn-link" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" onclick="configureWidget(<?= $widget->id ?>)">
                                <i class="fas fa-cog"></i> Configure
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="deleteWidget(<?= $widget->id ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body widget-content" id="widget-<?= $widget->id ?>">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Widget Modal -->
<div class="modal fade" id="addWidgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Widget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="widgetForm">
                    <div class="mb-3">
                        <label class="form-label">Widget Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Widget Type</label>
                        <select class="form-select" name="type" required>
                            <option value="delivery_rate">Delivery Rate</option>
                            <option value="message_volume">Message Volume</option>
                            <option value="peak_times">Peak Sending Times</option>
                            <option value="recent_messages">Recent Messages</option>
                        </select>
                    </div>
                    <div id="widgetConfig" class="mb-3">
                        <!-- Configuración dinámica basada en el tipo de widget -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveWidget()">Add Widget</button>
            </div>
        </div>
    </div>
</div>

<!-- Widget Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
// Inicializar Sortable para el grid de widgets
const widgetGrid = document.getElementById('widgetGrid');
Sortable.create(widgetGrid, {
    animation: 150,
    onEnd: function() {
        const positions = {};
        document.querySelectorAll('[data-widget-id]').forEach((widget, index) => {
            positions[widget.dataset.widgetId] = index;
        });
        updateWidgetPositions(positions);
    }
});

// Cargar datos de widgets
function loadWidgetData(widgetId) {
    fetch(`/widget/getData?id=${widgetId}`)
        .then(response => response.json())
        .then(data => {
            const widget = document.getElementById(`widget-${widgetId}`);
            renderWidgetContent(widget, data);
        });
}

// Renderizar contenido del widget
function renderWidgetContent(widget, data) {
    // Implementar renderizado específico para cada tipo de widget
}

// Guardar nuevo widget
async function saveWidget() {
    const form = document.getElementById('widgetForm');
    const formData = new FormData(form);
    
    const response = await fetch('/widget/save', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

// Actualizar posiciones de widgets
async function updateWidgetPositions(positions) {
    await fetch('/widget/updatePositions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(positions)
    });
}

// Eliminar widget
async function deleteWidget(widgetId) {
    if (!confirm('Are you sure you want to delete this widget?')) return;
    
    const response = await fetch(`/widget/delete/${widgetId}`);
    const result = await response.json();
    
    if (result.success) {
        document.querySelector(`[data-widget-id="${widgetId}"]`).remove();
    }
}

// Cargar datos iniciales de widgets
document.querySelectorAll('[data-widget-id]').forEach(widget => {
    loadWidgetData(widget.dataset.widgetId);
});
</script>

<!-- Add this before the charts section -->
<div class="export-buttons mb-4">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Export Report
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">Export as PDF</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">Export as Excel</a></li>
        </ul>
    </div>
</div>

<!-- Add this at the end of the file -->
<script>
function exportReport(format) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const type = document.getElementById('report_type').value;
    
    window.location.href = `index.php?controller=report&action=exportReport&format=${format}&type=${type}&start_date=${startDate}&end_date=${endDate}`;
}
</script>