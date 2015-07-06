<?php
abstract class __convead_events {
    static $orderAmount;
    static $orderPrice;

    public function onOrderRefreshConveadUpdateCart(iUmiEventPoint $eventPoint) {
        if($eventPoint->getMode() !== 'after') {
            return true;
        }

        $path = getRequest('path');

        if(!$path || strpos($path, 'emarket/basket/') === false) {
            return true;
        }

        $order = $eventPoint->getRef('order');

        if($order->getValue('status_id')) {
            return true;
        }

        $permissions = permissionsCollection::getInstance();
        $visitorUid = ($permissions->isAuth()) ? $order->getValue('customer_id') : false;

        $api = $this->getConveadApiTracker($visitorUid);

        if(!$api instanceof ConveadTracker) {
            return true;
        }

        $amount = $eventPoint->getParam('totalAmount');
        $price = $eventPoint->getRef('actualPrice');

        if(is_null(self::$orderAmount) && is_null(self::$orderPrice)) {
            self::$orderAmount = $amount;
            self::$orderPrice = $price;
            return true;
        }

        if(self::$orderAmount == $amount && self::$orderPrice == $price) {
            return true;
        }

        self::$orderAmount = $amount;
        self::$orderPrice = $price;

        $items = $eventPoint->getParam('items');

        $conveadItems = array();

        /* @var orderItem[] $items */

        foreach ($items as $orderItem) {
            $item = array(
                'product_id' => $orderItem->getItemElement()->getId(),
                'qnt' => $orderItem->getAmount(),
                'price' => $orderItem->getItemPrice()
            );

            $conveadItems[] = $item;
        }

        $api->eventUpdateCart($conveadItems);
    }

    public function onOrderStatusChangedConveadPurchase(iUmiEventPoint $eventPoint) {
        if($eventPoint->getMode() !== 'after') {
            return true;
        }

        $oldStatusId = $eventPoint->getParam('old-status-id');
        $newStatusId = $eventPoint->getParam('new-status-id');

        if(!is_null($oldStatusId) || $newStatusId != order::getStatusByCode('waiting')) {
            return true;
        }

        $order = $eventPoint->getRef('order');
        $customerId = $order->getValue('purchaser_one_click');
        $visitorUid = $order->getCustomerId();

        if(!$customerId) {
            $customerId = $order->getCustomerId();
        }

        $customer = umiObjectsCollection::getInstance()->getObject($customerId);

        if(!$customer) {
            return true;
        }

        $visitorInfo = ($customer) ? $this->getConveadVisitorInfo($customer) : false;

        if(!permissionsCollection::getInstance()->isAuth()) {
            $visitorUid = false;
        }

        /* @var order $order */

        $api = $this->getConveadApiTracker($visitorUid, $visitorInfo);

        if(!$api instanceof ConveadTracker) {
            return true;
        }

        $items = $order->getItems();

        /* @var orderItem[] $items */

        $conveadItems = array();

        foreach($items as $item) {
            $item = array(
                'product_id' => $item->getItemElement()->getId(),
                'qnt' => $item->getAmount(),
                'price' => $item->getItemPrice(),
            );

            $conveadItems[] = $item;
        }

        $api->eventOrder($order->getValue('number'), $order->getActualPrice(), $conveadItems, $visitorInfo);
    }
};