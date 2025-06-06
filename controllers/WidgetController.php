<?php
class WidgetController extends Controller {
    private $widgetModel;
    private $messageModel;

    public function __construct() {
        parent::__construct();
        $this->widgetModel = $this->model('Widget');
        $this->messageModel = $this->model('Message');
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    public function getWidgetData() {
        $widgetId = $_GET['id'] ?? 0;
        $widget = $this->widgetModel->getWidgetById($widgetId);
        $data = [];

        switch ($widget->widget_type) {
            case 'delivery_rate':
                $data = $this->messageModel->getDeliveryStats($_SESSION['user_id']);
                break;
            case 'message_volume':
                $period = json_decode($widget->config)->period ?? 'week';
                $data = $this->messageModel->getMessageStats(
                    $_SESSION['user_id'],
                    $period,
                    'all'
                );
                break;
            case 'peak_times':
                $data = $this->messageModel->getPeakSendingTimes($_SESSION['user_id']);
                break;
            case 'recent_messages':
                $limit = json_decode($widget->config)->limit ?? 5;
                $data = $this->messageModel->getRecentMessages($_SESSION['user_id'], $limit);
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function saveWidget() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $title = $_POST['title'];
            $config = $_POST['config'] ?? '{}';
            $position = $_POST['position'] ?? 0;

            if ($this->widgetModel->saveWidget(
                $_SESSION['user_id'],
                $type,
                $title,
                $config,
                $position
            )) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }

    public function updatePositions() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $positions = json_decode(file_get_contents('php://input'), true);
            $success = true;

            foreach ($positions as $widgetId => $position) {
                if (!$this->widgetModel->updateWidgetPosition($widgetId, $position)) {
                    $success = false;
                    break;
                }
            }

            echo json_encode(['success' => $success]);
        }
    }

    public function deleteWidget($id) {
        if ($this->widgetModel->deleteWidget($id, $_SESSION['user_id'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}