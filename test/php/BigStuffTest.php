<?php
namespace AcmeProject;

use PHPUnit\Framework\TestCase;
use AcmeProject\BigStuff as Bigstuff;

function sleep(): void
{
    return;
}

final class BigStuffTest extends TestCase
{
    public function testProcessOrder(): void
    {
        $big_stuff = new BigStuff;
        $this->assertSame('Easter Basket (Big)', $big_stuff->processOrder('Easter Basket (Big)'));
        $this->assertSame('Easter Basket (Small)', $big_stuff->processOrder('Easter Basket (Small)'));
        $this->assertSame(
            'Easter Basket (Big), Easter Basket (Small)',
            $big_stuff->processOrder(array('Easter Basket (Big)', 'Easter Basket (Small)'))
        );
        $this->assertSame(
            'Easter Basket (Small), Easter Basket (Big)',
            $big_stuff->processOrder(array('Easter Basket (Small)', 'Easter Basket (Big)'))
        );
    }

    public function testInvalidOrderCannotBeProcessed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $big_stuff = new BigStuff;
        $big_stuff->processOrder('invalid');
    }

    public function testGetAvgTimeStartsAtBestCase(): void
    {
        $big_stuff = new BigStuff;
        $this->assertSame(1.0, $big_stuff->getAvgTime());
    }

    public function testSimulateAThousandOrders(): void
    {
        $big_stuff = new BigStuff;
        for($i = 0; $i <= 1000; ++$i) {
            $big_stuff->processOrder('Easter Basket (Small)');
            $this->assertEquals(1.5, $big_stuff->getAvgTime(), '', 0.5);
        }
    }
}
?>
