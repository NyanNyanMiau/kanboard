<?php

require_once 'tests/units/Base.php';

use Kanboard\Core\Plugin\Loader;
use Kanboard\Plugin\Timetable\Model\Timetable;
use Kanboard\Plugin\Timetable\Model\TimetableDay;
use Kanboard\Plugin\Timetable\Model\TimetableWeek;
use Kanboard\Plugin\Timetable\Model\TimetableOff;
use Kanboard\Plugin\Timetable\Model\TimetableExtra;

class TimetableTest extends Base
{
    public function setUp()
    {
        parent::setUp();

        $plugin = new Loader($this->container);
        $plugin->scan();
    }

    public function testCalculateWorkDays()
    {
        $w = new TimetableWeek($this->container);
        $t = new Timetable($this->container);

        $this->assertNotFalse($w->create(1, 1, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 1, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 2, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 2, '13:00', '17:00'));

        $monday = new DateTime('next Monday');

        $timetable = $t->calculate(1, $monday, new DateTime('next Monday + 6 days'));
        $this->assertNotEmpty($timetable);
        $this->assertCount(4, $timetable);

        $this->assertEquals($monday->format('Y-m-d').' 09:30', $timetable[0][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 12:00', $timetable[0][1]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 13:00', $timetable[1][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 17:00', $timetable[1][1]->format('Y-m-d H:i'));

        $this->assertEquals($monday->add(new DateInterval('P1D'))->format('Y-m-d').' 09:30', $timetable[2][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 12:00', $timetable[2][1]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 13:00', $timetable[3][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 17:00', $timetable[3][1]->format('Y-m-d H:i'));
    }

    public function testCalculateOverTime()
    {
        $d = new TimetableDay($this->container);
        $w = new TimetableWeek($this->container);
        $e = new TimetableExtra($this->container);
        $t = new Timetable($this->container);

        $monday = new DateTime('next Monday');
        $tuesday = new DateTime('next Monday + 1 day');
        $friday = new DateTime('next Monday + 4 days');

        $this->assertNotFalse($d->create(1, '08:00', '12:00'));
        $this->assertNotFalse($d->create(1, '14:00', '18:00'));

        $this->assertNotFalse($w->create(1, 1, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 1, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 2, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 2, '13:00', '17:00'));

        $this->assertNotFalse($e->create(1, $tuesday->format('Y-m-d'), 0, '17:00', '22:00'));
        $this->assertNotFalse($e->create(1, $friday->format('Y-m-d'), 1));

        $timetable = $t->calculate(1, $monday, new DateTime('next Monday + 6 days'));
        $this->assertNotEmpty($timetable);
        $this->assertCount(7, $timetable);

        $this->assertEquals($monday->format('Y-m-d').' 09:30', $timetable[0][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 12:00', $timetable[0][1]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 13:00', $timetable[1][0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 17:00', $timetable[1][1]->format('Y-m-d H:i'));

        $this->assertEquals($tuesday->format('Y-m-d').' 09:30', $timetable[2][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 12:00', $timetable[2][1]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 13:00', $timetable[3][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 17:00', $timetable[3][1]->format('Y-m-d H:i'));

        $this->assertEquals($tuesday->format('Y-m-d').' 17:00', $timetable[4][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 22:00', $timetable[4][1]->format('Y-m-d H:i'));

        $this->assertEquals($friday->format('Y-m-d').' 08:00', $timetable[5][0]->format('Y-m-d H:i'));
        $this->assertEquals($friday->format('Y-m-d').' 12:00', $timetable[5][1]->format('Y-m-d H:i'));

        $this->assertEquals($friday->format('Y-m-d').' 14:00', $timetable[6][0]->format('Y-m-d H:i'));
        $this->assertEquals($friday->format('Y-m-d').' 18:00', $timetable[6][1]->format('Y-m-d H:i'));
    }

    public function testCalculateTimeOff()
    {
        $d = new TimetableDay($this->container);
        $w = new TimetableWeek($this->container);
        $o = new TimetableOff($this->container);
        $t = new Timetable($this->container);

        $monday = new DateTime('next Monday');
        $tuesday = new DateTime('next Monday + 1 day');
        $friday = new DateTime('next Monday + 4 days');

        $this->assertNotFalse($d->create(1, '08:00', '12:00'));
        $this->assertNotFalse($d->create(1, '14:00', '18:00'));

        $this->assertNotFalse($w->create(1, 1, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 1, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 2, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 2, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 5, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 5, '13:00', '17:00'));

        $this->assertNotFalse($o->create(1, $tuesday->format('Y-m-d'), 0, '14:00', '15:00'));
        $this->assertNotFalse($o->create(1, $monday->format('Y-m-d'), 1));

        $timetable = $t->calculate(1, $monday, new DateTime('next Monday + 6 days'));
        $this->assertNotEmpty($timetable);
        $this->assertCount(5, $timetable);

        $this->assertEquals($tuesday->format('Y-m-d').' 09:30', $timetable[0][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 12:00', $timetable[0][1]->format('Y-m-d H:i'));

        $this->assertEquals($tuesday->format('Y-m-d').' 13:00', $timetable[1][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 14:00', $timetable[1][1]->format('Y-m-d H:i'));

        $this->assertEquals($tuesday->format('Y-m-d').' 15:00', $timetable[2][0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 17:00', $timetable[2][1]->format('Y-m-d H:i'));

        $this->assertEquals($friday->format('Y-m-d').' 09:30', $timetable[3][0]->format('Y-m-d H:i'));
        $this->assertEquals($friday->format('Y-m-d').' 12:00', $timetable[3][1]->format('Y-m-d H:i'));

        $this->assertEquals($friday->format('Y-m-d').' 13:00', $timetable[4][0]->format('Y-m-d H:i'));
        $this->assertEquals($friday->format('Y-m-d').' 17:00', $timetable[4][1]->format('Y-m-d H:i'));
    }

    public function testClosestTimeSlot()
    {
        $w = new TimetableWeek($this->container);
        $t = new Timetable($this->container);

        $this->assertNotFalse($w->create(1, 1, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 1, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 2, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 2, '13:00', '17:00'));

        $monday = new DateTime('next Monday');
        $tuesday = new DateTime('next Monday + 1 day');

        $timetable = $t->calculate(1, new DateTime('next Monday'), new DateTime('next Monday + 6 days'));
        $this->assertNotEmpty($timetable);
        $this->assertCount(4, $timetable);

        // Start to work before timetable
        $date = clone($monday);
        $date->setTime(5, 02);

        $slot = $t->findClosestTimeSlot($date, $timetable);
        $this->assertNotEmpty($slot);
        $this->assertEquals($monday->format('Y-m-d').' 09:30', $slot[0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 12:00', $slot[1]->format('Y-m-d H:i'));

        // Start to work at the end of the timeslot
        $date = clone($monday);
        $date->setTime(12, 02);

        $slot = $t->findClosestTimeSlot($date, $timetable);
        $this->assertNotEmpty($slot);
        $this->assertEquals($monday->format('Y-m-d').' 09:30', $slot[0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 12:00', $slot[1]->format('Y-m-d H:i'));

        // Start to work at lunch time
        $date = clone($monday);
        $date->setTime(12, 32);

        $slot = $t->findClosestTimeSlot($date, $timetable);
        $this->assertNotEmpty($slot);
        $this->assertEquals($monday->format('Y-m-d').' 13:00', $slot[0]->format('Y-m-d H:i'));
        $this->assertEquals($monday->format('Y-m-d').' 17:00', $slot[1]->format('Y-m-d H:i'));

        // Start to work early in the morning
        $date = clone($tuesday);
        $date->setTime(8, 02);

        $slot = $t->findClosestTimeSlot($date, $timetable);
        $this->assertNotEmpty($slot);
        $this->assertEquals($tuesday->format('Y-m-d').' 09:30', $slot[0]->format('Y-m-d H:i'));
        $this->assertEquals($tuesday->format('Y-m-d').' 12:00', $slot[1]->format('Y-m-d H:i'));
    }

    public function testCalculateDuration()
    {
        $w = new TimetableWeek($this->container);
        $t = new Timetable($this->container);

        $this->assertNotFalse($w->create(1, 1, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 1, '13:00', '17:00'));
        $this->assertNotFalse($w->create(1, 2, '09:30', '12:00'));
        $this->assertNotFalse($w->create(1, 2, '13:00', '17:00'));

        $monday = new DateTime('next Monday');
        $tuesday = new DateTime('next Monday + 1 day');

        // Different day
        $start = clone($monday);
        $start->setTime(16, 02);

        $end = clone($tuesday);
        $end->setTime(10, 03);

        $this->assertEquals(1.52, $t->calculateEffectiveDuration(1, $start, $end));

        // Same time slot
        $start = clone($monday);
        $start->setTime(16, 02);

        $end = clone($monday);
        $end->setTime(17, 03);

        $this->assertEquals(0.96, $t->calculateEffectiveDuration(1, $start, $end), '', 0.1);

        // Intermediate time slot
        $start = clone($monday);
        $start->setTime(10, 02);

        $end = clone($tuesday);
        $end->setTime(16, 03);

        $this->assertEquals(11.52, $t->calculateEffectiveDuration(1, $start, $end));

        // Different day
        $start = clone($monday);
        $start->setTime(9, 02);

        $end = clone($tuesday);
        $end->setTime(10, 03);

        $this->assertEquals(7.04, $t->calculateEffectiveDuration(1, $start, $end), '', 0.1);

        // Start before first time slot
        $start = clone($monday);
        $start->setTime(5, 32);

        $end = clone($tuesday);
        $end->setTime(11, 17);

        $this->assertEquals(8.25, $t->calculateEffectiveDuration(1, $start, $end), '', 0.1);
    }

    public function testCalculateDurationWithEmptyTimetable()
    {
        $t = new Timetable($this->container);

        $start = new DateTime('next Monday');
        $start->setTime(16, 02);

        $end = new DateTime('next Monday');
        $end->setTime(17, 03);

        $this->assertEquals(1.02, $t->calculateEffectiveDuration(1, $start, $end));
    }
}
