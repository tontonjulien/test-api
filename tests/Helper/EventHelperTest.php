<?php

namespace App\Tests\Helper;

use App\Helper\EventHelper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class EventHelperTest extends TestCase
{
    public function testGetEventsByHour()
    {
        $helper = new EventHelper();
        $iteration = 6;
        $input = $helper->generateData($iteration);
        $output = $helper->getEventsByHour($input);
        foreach ($output as $item) {
            $this->assertArrayHasKey('heure', $item);
            $this->assertArrayHasKey('impressions', $item);
            $this->assertArrayHasKey('video_start', $item);
            $this->assertArrayHasKey('video_25', $item);
            $this->assertArrayHasKey('video_50', $item);
            $this->assertArrayHasKey('video_75', $item);
            $this->assertArrayHasKey('video_completed', $item);
            $this->assertEquals(0, (Carbon::createFromFormat('d-m-y H:i', $item['heure']))->minute);
        }

        $expected = [
            [
                'heure' => '03-05-21 18:00',
                'impressions' => $iteration,
                'video_start' => $iteration,
                'video_25' => $iteration,
                'video_50' => $iteration,
                'video_75' => $iteration,
                'video_completed' => $iteration,
            ],
        ];
        $this->assertEquals($expected, $output);

        $input = $helper->generateData($iteration, 2);
        $output = $helper->getEventsByHour($input);
        $this->assertTrue($output[0]['impressions'] + $output[1]['impressions'] === $iteration);
    }

    public function testAddMultiplierColumn()
    {
        $helper = new EventHelper();
        $iteration = 6;
        $input = $helper->generateData($iteration);
        $output = $helper->addMultiplierColumn($helper->getEventsByHour($input));

        $this->assertArrayHasKey('multiplied_impressions', $output[0]);
        $this->assertSame(number_format($iteration * EventHelper::MULTIPLIER, 1), $output[0]['multiplied_impressions']);
    }

    public function testReturnUniqueIds()
    {
        $helper = new EventHelper();
        $input = $helper->generateData();
        $this->assertEquals($helper->returnUniqueIds($input), array_unique($helper->returnIds($input)));
    }
}
