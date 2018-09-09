<?php
namespace AcmeProject;

final class LittleStuff
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
        if (strcmp($item,  'Toy Easter Egg') != 0) {
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
        $this->avg_time = 7.5;
        $this->num_orders = 1;
    }

    public function processOrder($order): string
    {
        $this->ensureIsValidOrder($order);
        $wait_time = 7.5 + 2.5 * (mt_rand() / mt_getrandmax());
        sleep($wait_time);
        $this->recalculateAvgTime($wait_time);
        return $order;

    }

    public function getAvgTime(): float
    {
        return $this->avg_time;
    }
}

?>
