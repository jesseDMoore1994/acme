<?php
namespace AcmeProject;
final class Acme
{
    private $valid_items = array('Easter Basket (Big)', 'Easter Basket (Small)',
        'Toy Easter Egg', 'Stuffed Bunny Rabbit');
    private $webroot = '127.0.0.1:8080/~jdm0032/';
    private $shipped_notices;
    private $orders_issued;
    private $assembled_orders;

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

    public function __construct()
    {
        $this->shipped_notices = [];
        $this->orders_issued = [];
        $this->assembled_orders = [];
    }

    public function processOrder($order, $shipto): string
    {
        #Make sure we have valid input
        $this->ensureIsValidOrder($order);
        if (gettype($order) == 'string') {
            $notice_str = date("H:i:s - ") . $order . ' shipped from Acme to ' . $shipto . '.';
            array_push($this->shipped_notices, $notice_str);
            return $order;
        } else {
            #handle for multicurl
            $mh = curl_multi_init();
            #start issuing orders if necessary
            $big_stuff_order_notice = NULL;
            $big_stuff_url = NULL;
            $big_stuff_ch = NULL;
            #figure out if we need to order from big stuff
            if(in_array('Easter Basket (Big)', $order) || in_array('Easter Basket (Small)', $order)) {
                if(in_array('Easter Basket (Big)', $order) && !in_array('Easter Basket (Small)', $order)) {
                    $big_stuff_order_notice = date("H:i:s - ") . 'Order For Easter Basket (Big) issued to Big Stuff.';
                    $big_stuff_url = $this->webroot . 'php/BigStuff.php?proc=processOrder&item[]=Easter%20Basket%20(Big)'
                                   . '&shipTo=Acme&mysession=' . $GLOBALS['mysession'];
                }
                if(!in_array('Easter Basket (Big)', $order) && in_array('Easter Basket (Small)', $order)) {
                    $big_stuff_order_notice = date("H:i:s - ") . 'Order For Easter Basket (Small) issued to Big Stuff.';
                    $big_stuff_url = $this->webroot . 'php/BigStuff.php?proc=processOrder&item[]=Easter%20Basket%20(Small)'
                                   . '&shipTo=Acme&mysession=' . $GLOBALS['mysession'];
                }
                if(in_array('Easter Basket (Big)', $order) && in_array('Easter Basket (Small)', $order)) {
                    $big_stuff_order_notice = date("H:i:s - ") . 'Order For Easter Basket (Small) and Easter Basket (Big) ' 
                                   . 'issued to Big Stuff.';
                    $big_stuff_url = $this->webroot . 'php/BigStuff.php?proc=processOrder&item[]=Easter%20Basket%20(Big)'
                                   . '&item[]=Easter%20Basket%20(Small)&shipTo=Acme&mysession=' . $GLOBALS['mysession'];
                }
                $big_stuff_ch = curl_init($big_stuff_url);
                curl_setopt($big_stuff_ch, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $big_stuff_ch); 
            }

            $little_stuff_order_notice = NULL;
            $little_stuff_url = NULL;
            $little_stuff_ch = NULL;
            #figure out if we need to order from little stuff
            if(in_array('Toy Easter Egg', $order)) {
                $little_stuff_order_notice = date("H:i:s - ") . 'Order For Toy Easter Egg issued to Little Stuff.';
                $little_stuff_url =  $this->webroot . 'php/LittleStuff.php?proc=processOrder&item=Toy%20Easter%20Egg'
                                   . '&shipTo=Acme&mysession=' . $GLOBALS['mysession'];
                $little_stuff_ch = curl_init($little_stuff_url);
                curl_setopt($little_stuff_ch, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $little_stuff_ch); 
            }

            #add order notice for big stuff if needed
            if(!is_null($big_stuff_order_notice)){
                array_push($this->orders_issued, $big_stuff_order_notice);
            }
            #add order notice for little stuff if needed
            if(!is_null($little_stuff_order_notice)){
                array_push($this->orders_issued, $little_stuff_order_notice);
            }

            #begin curl request logic
            if(!is_null($little_stuff_ch) || !is_null($big_stuff_ch)){
                $running = NULL;
				
                session_write_close();
                do {
                    curl_multi_exec($mh, $running);
                } while ($running);
				
				//close the handles if open
				if(!is_null($big_stuff_ch)){
					curl_multi_remove_handle($mh, $big_stuff_ch);
                    curl_close($big_stuff_ch);
				}
				if(!is_null($little_stuff_ch)){
					curl_multi_remove_handle($mh, $little_stuff_ch);
                    curl_close($little_stuff_ch);
				}
				curl_multi_close($mh);

            }
            
            return implode(', ', $order);
        } 

    }

    public function createAssembleAndShipNotices($order, $shipto): void
    {
        if(in_array('Toy Easter Egg', $order) || in_array('Easter Basket (Big)', $order) || in_array('Easter Basket (Small)', $order))
        {
            $assembled_str = date("H:i:s - ") . implode(', ', $order) . ' assembled at Acme.';
            array_push($this->assembled_orders, $assembled_str);
        }

        $notice_str = date("H:i:s - ") . implode(', ', $order) . ' shipped from Acme to ' . $shipto . '.';
        array_push($this->shipped_notices, $notice_str);

    }

    public function getShippedNotices(): array
    {
        $notices = $this->shipped_notices;
        return $notices;
    }

    public function getOrderNotices(): array
    {
        $orders = $this->orders_issued;
        return $orders;
    }

    public function getAssemblyNotices(): array
    {
        $orders = $this->assembled_orders;
        return $orders;
    }
}
$acme = NULL;
if(isset($_COOKIE['mycookie'])) {
    session_id($_COOKIE['mycookie']);
}
session_start();
$GLOBALS['mysession'] = session_id();
if(isset($_SESSION['acme'])) {
    $acme = $_SESSION['acme'];
} else {
    $acme = new Acme;
    $_SESSION['acme'] = $acme;
    $_COOKIE['mycookie'] = session_id();
}
if($_REQUEST['proc'] == 'processOrder') { 
    $items_str = $acme->processOrder($_REQUEST['item'], $_REQUEST['shipTo']);
    if(strpos($items_str, 'Easter Basket') !== false || strpos($items_str, 'Toy Easter Egg') !== false)
        session_start();
    $_SESSION['acme']->createAssembleAndShipNotices($_REQUEST['item'], $_REQUEST['shipTo']);
    printf("%s", $items_str);
}
if($_REQUEST['proc'] == 'getShippedNotices') {
    printf("%s", implode('&#13;&#10;', $acme->getShippedNotices()));
}
if($_REQUEST['proc'] == 'getOrderNotices') {
    printf("%s", implode('&#13;&#10;', $acme->getOrderNotices()));
}
if($_REQUEST['proc'] == 'getAssemblyNotices') {
    printf("%s", implode('&#13;&#10;', $acme->getAssemblyNotices()));
}
if($_REQUEST['proc'] == 'getSessionId') {
    printf("%s", session_id());
}
?>
