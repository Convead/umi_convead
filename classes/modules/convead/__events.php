<?php
abstract class __convead_events {

    public function onOrderRefreshConveadUpdateCart(iUmiEventPoint $eventPoint) {
        if($eventPoint->getMode() !== 'after') return true;

        $path = getRequest('path');

        if(!$path || strpos($path, 'emarket/basket/') === false) return true;

        $order = $eventPoint->getRef('order');

        if($order->getValue('status_id')) return true;

        $permissions = permissionsCollection::getInstance();
        $visitorUid = ($permissions->isAuth()) ? $order->getValue('customer_id') : false;

        $api = $this->getConveadTracker($visitorUid);

        /* блокировать отправку события update_cart для ложных вызовов */
        if ($api->generated_uid) return false;

        if(!$api instanceof ConveadTracker) return true;

        $items = $eventPoint->getParam('items');

        $conveadItems = array();

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

    public function onOrderStatusChangedConveadOrderState(iUmiEventPoint $eventPoint) {
        if($eventPoint->getMode() !== 'after') return true;

        $oldStatusId = $eventPoint->getParam('old-status-id');
        $newStatusId = $eventPoint->getParam('new-status-id');
        $order = $eventPoint->getRef('order');

        $state = $this->switchState( order::getCodeByStatus($newStatusId) );

        $orderData = $this->getOrderData($order);

        if(!is_null($oldStatusId) or $newStatusId == order::getStatusByCode('waiting')) {
            $customerId = $order->getValue('purchaser_one_click');
            $visitorUid = $order->getCustomerId();

            if(!$customerId) $customerId = $order->getCustomerId();

            $customer = umiObjectsCollection::getInstance()->getObject($customerId);

            if(!$customer) return true;

            $visitorInfo = ($customer) ? $this->getConveadVisitorInfo($customer) : false;

            if(!permissionsCollection::getInstance()->isAuth()) $visitorUid = false;

            $tracker = $this->getConveadTracker($visitorUid, $visitorInfo);
            if(!$tracker) return true;
            $tracker->eventOrder($orderData->order_id, $orderData->revenue, $orderData->items, $orderData->state);
        }
        else {
            $tracker = $this->getConveadTrackerAnonym();
            if(!$tracker) return true;
            $tracker->webHookOrderUpdate($orderData->order_id, $orderData->state, $orderData->revenue, $orderData->items);
        }
        return true;
    }
    
    /* изменение статуса на странице заказа */
    public function onSystemModifyObjectConvead(iUmiEventPoint $event) {  
        if($event->getMode() !== 'after') return true;
        
        $object = $event->getRef('object');
        
        if($object instanceof iUmiObject) {
            $order = order::get($object->getId());
            $orderData = $this->getOrderData($order);

            #$state = $this->switchState( order::getCodeByStatus($object->getId()) );

            $tracker = $this->getConveadTrackerAnonym();
            if(!$tracker) return true;

            $tracker->webHookOrderUpdate($orderData->order_id, $orderData->state, $orderData->revenue, $orderData->items);
        }
        return true;
    }
    
    /* изменение статуса в списке заказов */
    public function onSystemModifyPropertyValueConvead(iUmiEventPoint $event) {  
        if($event->getMode() !== 'after') return true;
        
        $entity = $event->getRef('entity');

        if($entity instanceof iUmiObject) {
            $order = order::get($entity->getId());
            $orderData = $this->getOrderData($order);
            #$state = $this->switchState( order::getCodeByStatus($event->getParam("newValue")) );

            $tracker = $this->getConveadTrackerAnonym();
            if(!$tracker) return true;

            $tracker->webHookOrderUpdate($orderData->order_id, $orderData->state);
        }
        return true;
    }

};