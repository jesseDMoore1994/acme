<?php
namespace AcmeProject;

final class LittleStuff
{
    private $avg_time;
    private $num_orders;
    private $shipped_notices;

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
            throw new \InvalidArgumentException(
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
        $this->shipped_notices = [];
    }

    public function processOrder($order, $shipto): string
    {
        $this->ensureIsValidOrder($order);
        $wait_time = 7.5 + 2.5 * (mt_rand() / mt_getrandmax());
        sleep($wait_time);
        $this->recalculateAvgTime($wait_time);
        $notice_str = $order . ' shipped from Little Stuff to ' . $shipto . '.';
        array_push($this->shipped_notices, $notice_str);
        return $order;

    }

    public function getAvgTime(): float
    {
        return $this->avg_time;
    }

    public function getShippedNotices(): array
    {
        $notices = $this->shipped_notices;
        $this->shipped_notices = [];
        return $notices;
    }
}
$little_stuff = NULL;
session_start();
if($_SESSION['little_stuff']) {
    $little_stuff = $_SESSION['little_stuff'];
} else {
    $little_stuff = new LittleStuff;
    $_SESSION['little_stuff'] = $little_stuff;
}
if($_REQUEST['proc'] == 'processOrder') { 
    printf("item: %s", $little_stuff->processOrder($_REQUEST['item'], $_REQUEST['shipTo']));
}
if($_REQUEST['proc'] == 'getAvgTime') { 
    printf("avg time: %f", $little_stuff->getAvgTime());
}
if($_REQUEST['proc'] == 'getShippedNotices') {
    printf("notices: %s", implode('; ', $little_stuff->getShippedNotices()));
}
?>
