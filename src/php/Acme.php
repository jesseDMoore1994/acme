<?php
namespace AcmeProject;
use AcmeProject\Order as Order;

final class Acme
{
    private static $avg_time;
    private static $num_orders;
    private $valid_items = array('Easter Basket (Big)', 'Easter Basket (Small)',
        'Toy Easter Egg', 'Stuffed Bunny Rabbit');

    private function recalculateAvgTime(float $wait_time): void
    {
        $this->avg_time = (($this->num_orders * $this->avg_time) + $wait_time) / ($this->num_orders + 1);
        $this->num_orders = $this->num_orders + 1;
    }

    private function validateItem($item): void
    {
        $valid = false;
        foreach  ($this->valid_items as &$valid_item) {
            if (strcmp($item,  $valid_item) == 0) {
                $valid = true;
            }
        }
        if(!$valid) {
            throw new \InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid item.',
                    $item
                )
            );
        }
        
    }

    private function ensureIsValidOrder($order): void
    {
        if(gettype($order) == 'string') {
            $this->validateItem($order);
        } else if (gettype($order) == 'array') {
            foreach ($order as &$item) {
                $this->validateItem($item);
            }
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'order format is not recognized.'
                )
            );
        }
    }

    private function processBigStuff($order): void
    {
        return;
    }

    private function processLittleStuff($order): void
    {
        return;
    }

    public function __construct()
    {
        $this->avg_time = 1.0;
        $this->num_orders = 1;
    }

    public function processOrder($order): string
    {
        #Make sure we have valid input
        $this->ensureIsValidOrder($order);
        $wait_time = 1 + 1 * (mt_rand() / mt_getrandmax());
        sleep($wait_time);
        $this->recalculateAvgTime($wait_time);
        if (gettype($order) == 'string') {
            return $order;
        } else {
            return implode(', ', $order);
        } 

    }

    public function getAvgTime(): float
    {
        return $this->avg_time;
    }
}

?>
