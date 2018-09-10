<?php
namespace AcmeProject;

use PHPUnit\Framework\TestCase;
use AcmeProject\Acme as Acme;
use AcmeProject\Order as Order;

final class AcmeTest extends TestCase
{
	public function powerSet($in,$minLength = 1) { 
	    $count = count($in); 
	    $members = pow(2,$count); 
	    $return = array(); 
	    for ($i = 0; $i < $members; $i++) { 
	        $b = sprintf("%0".$count."b",$i); 
	        $out = array(); 
	        for ($j = 0; $j < $count; $j++) { 
	            if ($b{$j} == '1') $out[] = $in[$j]; 
	        } 
	        if (count($out) >= $minLength) { 
	            $return[] = array($out); 
	        } 
	    } 
	    return $return; 
	} 

    public function providerOrderData(): array {
        $pwr_set = $this->powerSet(
            array(
                'Easter Basket (Big)', 'Easter Basket (Small)', 
                'Toy Easter Egg', 'Stuffed Bunny Rabbit'
            )
        );
        return $pwr_set;
    }
    
    /**
     * @dataProvider providerOrderData
     */
    public function testProcessOrder($order_items): void
    {
        $acme = new Acme;
        $this->assertSame(
            implode(', ', $order_items),
            $acme->processOrder($order_items)
        );
    }

    public function testInvalidOrderCannotBeProcessed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $acme = new Acme;
        $acme->processOrder('invalid');
    }

    public function testGetAvgTimeStartsAtBestCase(): void
    {
        $acme = new Acme;
        $this->assertSame(1.0, $acme->getAvgTime());
    }

    #public function testSimulateAThousandOrders(): void
    #{
    #    $acme = new Acme;
    #    for($i = 0; $i <= 1000; ++$i) {
    #        $acme->processOrder('Easter Basket (Small)');
    #        $this->assertEquals(1.5, $acme->getAvgTime(), '', 0.5);
    #    }
    #}
}
?>
