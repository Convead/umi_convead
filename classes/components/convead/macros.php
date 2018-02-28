<?php

class ConveadMacros {

	public $module;

    public function __construct($module) {
        $this->module = is_null($this->module) ? $module : $this->module;

        $this->convead = $this->module->getImplementedInstance('ConveadLibrary');
    }

    public function onOrderRefreshConveadUpdateCart(iUmiEventPoint $eventPoint) {
        if($eventPoint->getMode() !== 'after') return true;

        $path = getRequest('path');
        if(!$path || strpos($path, 'emarket/basket/') === false) return true;

        $order = $eventPoint->getRef('order');

        $permissions = permissionsCollection::getInstance();
        $visitorUid = ($permissions->isAuth()) ? $order->getValue('customer_id') : false;

        $api = $this->convead->getConveadTracker($visitorUid);

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

        $state = $this->convead->switchState( order::getCodeByStatus($newStatusId) );

        $orderData = $this->convead->getOrderData($order);

        if(!is_null($oldStatusId) or $newStatusId == order::getStatusByCode('waiting')) {
            $customerId = $order->getValue('purchaser_one_click');
            $visitorUid = $order->getCustomerId();

            if(!$customerId) $customerId = $order->getCustomerId();

            $customer = umiObjectsCollection::getInstance()->getObject($customerId);

            if(!$customer) return true;

            $visitorInfo = ($customer) ? $this->convead->getConveadVisitorInfo($customer) : false;

            if(!permissionsCollection::getInstance()->isAuth()) $visitorUid = false;

            $tracker = $this->convead->getConveadTracker($visitorUid, $visitorInfo);
            if(!$tracker) return true;
            $tracker->eventOrder($orderData->order_id, $orderData->revenue, $orderData->items, $orderData->state);
        }
        else {
            $tracker = $this->convead->getConveadTrackerAnonym();
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
            $orderData = $this->convead->getOrderData($order);

            #$state = $this->convead->switchState( order::getCodeByStatus($object->getId()) );

            $tracker = $this->convead->getConveadTrackerAnonym();
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
            $orderData = $this->convead->getOrderData($order);
            #$state = $this->convead->switchState( order::getCodeByStatus($event->getParam("newValue")) );

            $tracker = $this->convead->getConveadTrackerAnonym();
            if(!$tracker) return true;

            $tracker->webHookOrderUpdate($orderData->order_id, $orderData->state);
        }
        return true;
    }
    
};
?>