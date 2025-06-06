<?php
class ReportController extends Controller {
    private $messageModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->messageModel = $this->model('Message');
        $this->userModel = $this->model('User');
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $data = [
            'total_messages' => $this->messageModel->getTotalMessages($userId),
            'messages_by_type' => $this->messageModel->getMessagesByType($userId),
            'delivery_stats' => $this->messageModel->getDeliveryStats($userId),
            'recent_messages' => $this->messageModel->getRecentMessages($userId, 5)
        ];
        $this->view('reports/index', $data);
    }

    public function getChartData() {
        $userId = $_SESSION['user_id'];
        $period = $_GET['period'] ?? 'week';
        $type = $_GET['type'] ?? 'all';
        
        $data = $this->messageModel->getMessageStats($userId, $period, $type);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function exportCsv() {
        $userId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'all';

        $data = $this->messageModel->getMessageReport($userId, $startDate, $endDate, $type);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="message_report.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Type', 'Subject', 'Recipients', 'Delivered', 'Failed']);
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
    }

    public function detailedAnalysis() {
        $userId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $data = [
            'delivery_time' => $this->messageModel->getDeliveryTimeAnalysis($userId, $startDate, $endDate),
            'peak_times' => $this->messageModel->getPeakSendingTimes($userId),
            'hourly_rate' => $this->messageModel->getDeliveryRateByHour($userId)
        ];

        $this->view('reports/detailed', $data);
    }

    public function generatePdf() {
        require_once APPROOT . '/libraries/tcpdf/tcpdf.php';

        $userId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Crear nuevo documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('MassMessage');
        $pdf->SetAuthor($_SESSION['user_name']);
        $pdf->SetTitle('Message Report');

        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Agregar página
        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'Message Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Estadísticas generales
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'General Statistics', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        $stats = $this->messageModel->getDeliveryStats($userId);
        $pdf->Cell(0, 8, 'Total Recipients: ' . $stats->total_recipients, 0, 1, 'L');
        $pdf->Cell(0, 8, 'Delivered: ' . $stats->delivered, 0, 1, 'L');
        $pdf->Cell(0, 8, 'Failed: ' . $stats->failed, 0, 1, 'L');
        $pdf->Ln(5);

        // Análisis por tipo de mensaje
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Message Type Analysis', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        $messagesByType = $this->messageModel->getMessagesByType($userId);
        foreach ($messagesByType as $type) {
            $pdf->Cell(0, 8, ucfirst($type->type) . ': ' . $type->count . ' messages', 0, 1, 'L');
        }
        $pdf->Ln(5);

        // Tiempos de entrega
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Delivery Time Analysis', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        $deliveryTime = $this->messageModel->getDeliveryTimeAnalysis($userId, $startDate, $endDate);
        foreach ($deliveryTime as $analysis) {
            $pdf->Cell(0, 8, ucfirst($analysis->type), 0, 1, 'L');
            $pdf->Cell(0, 8, '  Average: ' . round($analysis->avg_delivery_time / 60, 2) . ' minutes', 0, 1, 'L');
            $pdf->Cell(0, 8, '  Minimum: ' . round($analysis->min_delivery_time / 60, 2) . ' minutes', 0, 1, 'L');
            $pdf->Cell(0, 8, '  Maximum: ' . round($analysis->max_delivery_time / 60, 2) . ' minutes', 0, 1, 'L');
            $pdf->Ln(2);
        }

        // Generar el PDF
        $pdf->Output('message_report.pdf', 'D');
    }

    public function schedules() {
        $scheduleModel = $this->model('ReportSchedule');
        $schedules = $scheduleModel->getSchedules($_SESSION['user_id']);
        $this->view('reports/schedules', ['schedules' => $schedules]);
    }

    public function createSchedule() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $scheduleModel = $this->model('ReportSchedule');
            
            $name = $_POST['name'];
            $frequency = $_POST['frequency'];
            $recipients = $_POST['recipients'];
            $reportType = $_POST['report_type'];
            $format = $_POST['format'];

            if ($scheduleModel->create(
                $_SESSION['user_id'],
                $name,
                $frequency,
                $recipients,
                $reportType,
                $format
            )) {
                flash('schedule_success', 'Report schedule created successfully');
                redirect('report/schedules');
            } else {
                flash('schedule_error', 'Error creating schedule', 'alert alert-danger');
                redirect('report/schedules');
            }
        }
    }

    private function sendScheduledReport($schedule) {
        $userId = $schedule->user_id;
        $startDate = date('Y-m-d', strtotime('-1 ' . $schedule->frequency));
        $endDate = date('Y-m-d');

        // Generar el reporte según el formato
        $reportContent = $schedule->format === 'pdf' 
            ? $this->generatePdfReport($userId, $startDate, $endDate)
            : $this->generateCsvReport($userId, $startDate, $endDate);

        // Enviar por correo
        $emailService = new EmailService();
        $recipients = explode(',', $schedule->recipients);

        foreach ($recipients as $recipient) {
            $emailService->send([
                'to' => trim($recipient),
                'subject' => "Scheduled Report: {$schedule->name}",
                'body' => "Please find attached your scheduled report.",
                'attachment' => [
                    'content' => $reportContent,
                    'name' => "report.{$schedule->format}",
                    'type' => $schedule->format === 'pdf' ? 'application/pdf' : 'text/csv'
                ]
            ]);
        }
    }

    public function trends() {
        $analyzer = new TrendAnalyzer();
        $userId = $_SESSION['user_id'];
        $period = $_GET['period'] ?? 'month';

        $data = [
            'trends' => $analyzer->analyzeDeliveryTrends($userId, $period),
            'predictions' => $analyzer->predictDeliveryRate($userId)
        ];

        $this->view('reports/trends', $data);
    }

    public function getNotifications() {
        $notificationService = new NotificationService();
        $notifications = $notificationService->getUserNotifications($_SESSION['user_id']);
        
        header('Content-Type: application/json');
        echo json_encode($notifications);
    }

    public function markNotificationRead($id) {
        $notificationService = new NotificationService();
        $success = $notificationService->markAsRead($id, $_SESSION['user_id']);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function exportReport() {
        $format = $_GET['format'] ?? 'pdf';
        $type = $_GET['type'] ?? 'summary';
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $exportService = new ExportService();
        $data = $this->getMessage()->getReportData($type, $startDate, $endDate);
        $headers = $this->getReportHeaders($type);
        $title = "Message Report - " . ucfirst($type) . " ($startDate to $endDate)";
        
        header('Content-Type: ' . ($format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
        header('Content-Disposition: attachment; filename="report.' . $format . '"');
        
        if ($format === 'pdf') {
            echo $exportService->exportToPDF($data, $title, $headers);
        } else {
            echo $exportService->exportToExcel($data, $title, $headers);
        }
        exit;
    }

    private function getReportHeaders($type) {
        $headers = [
            'summary' => ['Date', 'Total Messages', 'Sent', 'Failed', 'Pending', 'Success Rate'],
            'detailed' => ['Message ID', 'Subject', 'Type', 'Recipients', 'Sent', 'Failed', 'Send Date', 'Status'],
            'trends' => ['Period', 'Messages Sent', 'Success Rate', 'Average Delivery Time', 'Peak Time']
        ];
        return $headers[$type] ?? $headers['summary'];
    }
}
use App\Models\UserActivity;
use App\Services\ExportService;