<div class="container-fluid py-4">
    <h2>User Activity Report</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <input type="hidden" name="controller" value="report">
                <input type="hidden" name="action" value="userActivity">
                
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $filters['user_id'] == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="login" <?= $filters['action'] == 'login' ? 'selected' : '' ?>>Login</option>
                        <option value="message_sent" <?= $filters['action'] == 'message_sent' ? 'selected' : '' ?>>Message Sent</option>
                        <option value="template_created" <?= $filters['action'] == 'template_created' ? 'selected' : '' ?>>Template Created</option>
                        <option value="contact_imported" <?= $filters['action'] == 'contact_imported' ? 'selected' : '' ?>>Contact Imported</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?>">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Activity Timeline</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportActivity('pdf')">PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportActivity('excel')">Excel</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?= $activity['created_at'] ?></td>
                                        <td><?= htmlspecialchars($activity['username']) ?></td>
                                        <td><?= htmlspecialchars($activity['action']) ?></td>
                                        <td><?= htmlspecialchars($activity['details']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Activity Statistics</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportActivity(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('controller', 'report');
    params.set('action', 'exportUserActivity');
    params.set('format', format);
    window.location.href = 'index.php?' + params.toString();
}

// Initialize activity chart
const ctx = document.getElementById('activityChart').getContext('2d');
const stats = <?= json_encode($stats) ?>;

const chartData = {
    labels: [...new Set(stats.map(s => s.date))],
    datasets: [...new Set(stats.map(s => s.action))].map(action => ({
        label: action,
        data: stats.filter(s => s.action === action).map(s => s.count),
        fill: false
    }))
};

new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

<!-- Add this after the activity timeline card -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Engagement Metrics</h5>
            </div>
            <div class="card-body">
                <canvas id="engagementChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Peak Activity Times</h5>
            </div>
            <div class="card-body">
                <canvas id="peakTimesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Retention Analysis</h5>
            </div>
            <div class="card-body">
                <canvas id="retentionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Add this before the closing body tag -->
<script>
// WebSocket connection
const ws = new WebSocket('ws://localhost:8080');

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'activity') {
        updateActivityTable(data.data);
        updateCharts(data.data);
    }
};

function updateActivityTable(activity) {
    const tbody = document.querySelector('table tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${activity.created_at}</td>
        <td>${activity.username}</td>
        <td>${activity.action}</td>
        <td>${activity.details}</td>
    `;
    tbody.insertBefore(row, tbody.firstChild);
}

// Initialize analytics charts
const analytics = <?= json_encode($analytics) ?>;

// Engagement Chart
const engagementCtx = document.getElementById('engagementChart').getContext('2d');
const engagementChart = new Chart(engagementCtx, {
    type: 'bar',
    data: {
        labels: ['Messages Sent', 'Templates Created', 'Contacts Imported'],
        datasets: [{
            data: [
                analytics.metrics.messages_sent,
                analytics.metrics.templates_created,
                analytics.metrics.contacts_imported
            ],
            backgroundColor: ['#4CAF50', '#2196F3', '#FFC107']
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

// Peak Times Chart
const peakTimesCtx = document.getElementById('peakTimesChart').getContext('2d');
const peakTimesChart = new Chart(peakTimesCtx, {
    type: 'line',
    data: {
        labels: analytics.peak_times.map(pt => pt.time_slot),
        datasets: [{
            label: 'Activity Count',
            data: analytics.peak_times.map(pt => pt.activity_count),
            borderColor: '#2196F3',
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

// Retention Chart
const retentionCtx = document.getElementById('retentionChart').getContext('2d');
const retentionChart = new Chart(retentionCtx, {
    type: 'scatter',
    data: {
        datasets: [{
            label: 'User Retention',
            data: analytics.retention.map(r => ({
                x: r.days_active,
                y: r.total_activities
            })),
            backgroundColor: '#4CAF50'
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { 
                title: { display: true, text: 'Days Active' }
            },
            y: { 
                title: { display: true, text: 'Total Activities' },
                beginAtZero: true
            }
        }
    }
});
</script>
</script>