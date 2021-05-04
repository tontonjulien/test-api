<?php


namespace App\Helper;


use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class EventHelper
{
    const MULTIPLIER = 0.1;

    /**
     * @param string $data
     * @return array
     */
    public function getEventsByHour(string $data): array
    {
        $events = [];
        $arrayData = json_decode($data, 1);
        foreach ($arrayData as $datum) {
            $events[$datum['timestamp']] = $datum;
        }
        ksort($events);

        $formattedData = [];
        foreach ($events as $timestamp => $event) {
            $eventTime = Carbon::createFromTimestamp($timestamp);
            $formattedHour = $eventTime->setMinute(0)->format('d-m-y H:i');

            if (array_key_exists($formattedHour, $formattedData) === false) {
                $counts = [
                    'impressions' => 0,
                    'video_start' => 0,
                    'video_25' => 0,
                    'video_50' => 0,
                    'video_75' => 0,
                    'video_completed' => 0,
                ];
                $counts[$event['event']] += 1;
                $formattedData[$formattedHour] = [
                    'heure' => $formattedHour,
                    'impressions' => $counts['impressions'],
                    'video_start' => $counts['video_start'],
                    'video_25' => $counts['video_25'],
                    'video_50' => $counts['video_50'],
                    'video_75' => $counts['video_75'],
                    'video_completed' => $counts['video_completed'],
                ];
            } else {
                $formattedData[$formattedHour][$event['event']] += 1;
            }
        }

        return array_values($formattedData);
    }

    /**
     * @param array $data
     * @return array
     */
    public function addMultiplierColumn(array $data): array
    {
        foreach ($data as $key => $datum) {
            $data[$key]['multiplied_impressions'] = number_format($datum['impressions'] * self::MULTIPLIER, 1);
        }

        return $data;
    }

    /**
     * @param array $data
     * @return false|resource
     */
    public function arrayToCsv(array $data)
    {
        $fields = array_keys($data[0]);
        $fp = fopen('php://output', 'w');

        fputcsv($fp, $fields);
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        return $fp;
    }

    /**
     * @param string $data
     * @return array
     */
    public function returnUniqueIds(string $data): array
    {
        return array_unique($this->returnIds($data));
    }

    /**
     * @param string $data
     * @return array
     */
    public function returnIds(string $data): array
    {
        $formattedData = json_decode($data, 1);
        $ids = [];
        foreach ($formattedData as $formattedDatum) {
            $ids[] = $formattedDatum['id'];
        }

        return ($ids);
    }

    public function generateData($iterations = 5, $hours = 1)
    {
        $data = [];
        $events = [
            'impressions',
            'video_start',
            'video_25',
            'video_50',
            'video_75',
            'video_completed',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($events as $event) {
                $timeStamp = Carbon::createFromTimestamp(rand(1620064801, 1620064801 + ($hours * 3600 ) - 1));
                $data[] = [
                    "id"        => Uuid::uuid4()->toString(),
                    "event"     => $event,
                    "timestamp" => $timeStamp->format('U'),
                ];
            }
        }

        return json_encode($data);
    }
}
