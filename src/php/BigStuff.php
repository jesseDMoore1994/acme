<?php
namespace AcmeProject;

final class BigStuff
{
    private static $avg_time;
    private static $num_orders;

    private function recalculateAvgTime(float $wait_time): void
    {
        $this->avg_time = (($this->num_orders * $this->avg_time) + $wait_time) / ($this->num_orders + 1);
        $this->num_orders = $this->num_orders + 1;
    }

    private function validateItem($item): void
    {
        if (strcmp($item,  'Easter Basket (Big)') != 0 && strcmp($item, 'Easter Basket (Small)') != 0) {
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

    public function __construct()
    {
        $this->avg_time = 1.0;
        $this->num_orders = 1;
    }

    public function processOrder($order): string
    {
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
