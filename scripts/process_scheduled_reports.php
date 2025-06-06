<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/ReportSchedule.php';
require_once __DIR__ . '/../controllers/ReportController.php';

$scheduleModel = new ReportSchedule();
$reportController = new ReportController();

$dueSchedules = $scheduleModel->getDueSchedules();

foreach ($dueSchedules as $schedule) {
    try {
        $reportController->sendScheduledReport($schedule);
        $scheduleModel->updateNextRun($schedule->id);
        echo "Processed schedule {$schedule->id} successfully\n";
    } catch (Exception $e) {
        echo "Error processing schedule {$schedule->id}: {$e->getMessage()}\n";
    }
}