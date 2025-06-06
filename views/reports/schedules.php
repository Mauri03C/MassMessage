<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Report Schedules</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
            Create New Schedule
        </button>
    </div>

    <?php flash('schedule_success'); ?>
    <?php flash('schedule_error'); ?>

    <!-- Schedules List -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($data['schedules'])): ?>
                <p class="text-center">No scheduled reports found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Frequency</th>
                                <th>Recipients</th>
                                <th>Report Type</th>
                                <th>Format</th>
                                <th>Next Run</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['schedules'] as $schedule): ?>
                            <tr>
                                <td><?= htmlspecialchars($schedule->name) ?></td>
                                <td><?= ucfirst($schedule->frequency) ?></td>
                                <td><?= htmlspecialchars($schedule->recipients) ?></td>
                                <td><?= ucfirst($schedule->report_type) ?></td>
                                <td><?= strtoupper($schedule->format) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($schedule->next_run)) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSchedule(<?= $schedule->id ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- New Schedule Modal -->
<div class="modal fade" id="newScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Report Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/report/createSchedule" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Schedule Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frequency</label>
                        <select class="form-select" name="frequency" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipients (comma-separated emails)</label>
                        <input type="text" class="form-control" name="recipients" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Report Type</label>
                        <select class="form-select" name="report_type" required>
                            <option value="summary">Summary Report</option>
                            <option value="detailed">Detailed Analysis</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format" required>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        window.location.href = `/report/deleteSchedule/${id}`;
    }
}
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>