<?php
class TrendAnalyzer {
    private $db;
    private $messageModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->messageModel = new Message();
    }

    public function analyzeDeliveryTrends($userId, $period = 'month') {
        $data = $this->messageModel->getMessageStats($userId, $period, 'all');
        $trends = [
            'success_rate' => [],
            'volume_trend' => [],
            'peak_hours' => [],
            'type_distribution' => []
        ];

        // Calcular tasa de éxito por día
        foreach ($data as $day) {
            $rate = $day->total > 0 ? ($day->delivered / $day->total) * 100 : 0;
            $trends['success_rate'][] = [
                'date' => $day->date,
                'rate' => round($rate, 2)
            ];
        }

        // Calcular tendencia de volumen
        $volumes = array_map(fn($day) => $day->total, $data);
        $trends['volume_trend'] = $this->calculateTrendLine($volumes);

        // Analizar horas pico
        $peakTimes = $this->messageModel->getPeakSendingTimes($userId);
        $trends['peak_hours'] = $this->analyzePeakHours($peakTimes);

        // Distribución por tipo de mensaje
        $typeStats = $this->messageModel->getMessagesByType($userId);
        foreach ($typeStats as $stat) {
            $trends['type_distribution'][$stat->type] = $stat->count;
        }

        return $trends;
    }

    public function predictDeliveryRate($userId) {
        $historicalData = $this->messageModel->getDeliveryStats($userId);
        $predictions = [];

        // Calcular predicción basada en promedios móviles
        $deliveryRates = array_map(function($day) {
            return $day->total_recipients > 0 
                ? ($day->delivered / $day->total_recipients) * 100 
                : 0;
        }, $historicalData);

        $movingAverage = $this->calculateMovingAverage($deliveryRates, 7);
        $trend = end($movingAverage);

        $predictions['next_day'] = [
            'expected_rate' => round($trend, 2),
            'confidence' => $this->calculateConfidence($deliveryRates)
        ];

        return $predictions;
    }

    private function calculateTrendLine($data) {
        $n = count($data);
        if ($n < 2) return ['slope' => 0, 'trend' => 'stable'];

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $data[$i];
            $sumXY += $i * $data[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);

        return [
            'slope' => $slope,
            'trend' => $slope > 0.1 ? 'increasing' : ($slope < -0.1 ? 'decreasing' : 'stable')
        ];
    }

    private function calculateMovingAverage($data, $window) {
        $result = [];
        $count = count($data);

        for ($i = 0; $i <= $count - $window; $i++) {
            $sum = 0;
            for ($j = 0; $j < $window; $j++) {
                $sum += $data[$i + $j];
            }
            $result[] = $sum / $window;
        }

        return $result;
    }

    private function calculateConfidence($data) {
        $mean = array_sum($data) / count($data);
        $variance = array_reduce($data, function($carry, $item) use ($mean) {
            return $carry + pow($item - $mean, 2);
        }, 0) / count($data);

        $stdDev = sqrt($variance);
        $confidence = 100 - min(($stdDev / $mean) * 100, 50);

        return max(round($confidence), 0);
    }

    private function analyzePeakHours($data) {
        $peaks = [];
        $maxCount = 0;

        foreach ($data as $hour) {
            if ($hour->count > $maxCount) {
                $maxCount = $hour->count;
                $peaks = [$hour->hour];
            } elseif ($hour->count == $maxCount) {
                $peaks[] = $hour->hour;
            }
        }

        return [
            'peak_hours' => $peaks,
            'max_volume' => $maxCount
        ];
    }
}