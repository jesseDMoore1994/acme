<?php
namespace AcmeProject;

use PHPUnit\Framework\TestCase;
use AcmeProject\LittleStuff as LittleStuff;

final class LittleStuffTest extends TestCase
{
    public function testProcessOrder(): void
    {
        $little_stuff = new LittleStuff;
        $this->assertSame('Toy Easter Egg', $little_stuff->processOrder('Toy Easter Egg'));
    }

    public function testInvalidOrderCannotBeProcessed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $little_stuff = new LittleStuff;
        $little_stuff->processOrder('invalid');
    }

    public function testGetAvgTimeStartsAtBestCase(): void
    {
        $little_stuff = new LittleStuff;
        $this->assertSame(7.5, $little_stuff->getAvgTime());
    }

    public function testSimulateAThousandOrders(): void
    {
        $little_stuff = new LittleStuff;
        for($i = 0; $i <= 1000; ++$i) {
            $little_stuff->processOrder('Toy Easter Egg');
            $this->assertEquals(8.75, $little_stuff->getAvgTime(), '', 1.25);
        }
    }
}
?>
