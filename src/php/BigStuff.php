<?php
namespace AcmeProject;

final class BigStuff
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
            throw new \InvalidArgumentException(
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
        $this->shipped_notices = [];
    }

    public function processOrder($order, $shipto): string
    {
        $this->ensureIsValidOrder($order);
        $wait_time = 1 + 1 * (mt_rand() / mt_getrandmax());
        sleep($wait_time);
        $this->recalculateAvgTime($wait_time);
        if (gettype($order) == 'string') {
            $notice_str = date("H:i:s - ") . $order . ' shipped from Big Stuff to ' . $shipto . '.';
            array_push($this->shipped_notices, $notice_str);
            return $order;
        } else {
            $notice_str = date("H:i:s - ") . implode(', ', $order) . ' shipped from Big Stuff to ' . $shipto . '.';
            array_push($this->shipped_notices, $notice_str);
            return implode(', ', $order);
        } 

    }

    public function getAvgTime(): float
    {
        return $this->avg_time;
    }

    public function getShippedNotices(): array
    {
        $notices = $this->shipped_notices;
        return $notices;
    }
}
$big_stuff = NULL;
if(isset($_REQUEST['mysession'])) { 
    session_id($_REQUEST['mysession']);
} 
session_start();
if(isset($_SESSION['big_stuff'])) {
    $big_stuff = $_SESSION['big_stuff'];
} else {
    $big_stuff = new BigStuff;
    $_SESSION['big_stuff'] = $big_stuff;
}
if($_REQUEST['proc'] == 'processOrder') { 
    printf("%s", $big_stuff->processOrder($_REQUEST['item'], $_REQUEST['shipTo']));
}
if($_REQUEST['proc'] == 'getAvgTime') { 
    printf("%f", $big_stuff->getAvgTime());
}
if($_REQUEST['proc'] == 'getShippedNotices') {
    printf("%s", implode('&#13;&#10;', $big_stuff->getShippedNotices()));
}
if($_REQUEST['proc'] == 'getSessionId') {
    printf("%s", session_id());
}
?>
