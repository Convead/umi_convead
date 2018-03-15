<?php
class ConveadLibrary {

    public function getConveadScript() {
        $key = $this->getConveadAppKey();

        if(!$key) return '';

        $conveadSettings = array();

        $visitorData = $this->getConveadVisitorData();

        if($visitorData) $conveadSettings = array_merge($conveadSettings, $visitorData);

        $conveadSettings['app_key'] = $key;

        $settings = json_encode($conveadSettings);

        $script = <<<END
<script type="text/javascript">
  window.ConveadSettings = $settings;
  (function(w,d,c){w[c]=w[c]||function(){(w[c].q=w[c].q||[]).push(arguments)};var ts = (+new Date()/86400000|0)*86400;var s = d.createElement('script');s.type = 'text/javascript';s.async = true;s.charset = 'utf-8';s.src = 'https://tracker.convead.io/widgets/'+ts+'/widget-{$key}.js';var x = d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);})(window,document,'convead');
</script>
END;

        $viewProduct = $this->getConveadViewProduct();

        if($viewProduct) {
            $productId = getArrayKey($viewProduct, 'product_id');
            $categoryId = getArrayKey($viewProduct, 'category_id');
            $productName = getArrayKey($viewProduct, 'product_name');
            $productUrl = getArrayKey($viewProduct, 'product_url');

            if($productId && $categoryId && $productName && $productUrl) {

                $viewProductScript = <<<END
<script type="text/javascript">
  convead('event', 'view_product', {
    product_id: '{$productId}',
    category_id: '{$categoryId}',
    product_name: '{$productName}',
    product_url: '{$productUrl}'
  });
</script>
END;

                $script .= $viewProductScript;
            }
        }

        return $script;
    }

    /**
     * Подготовить данные о текущем пользователе
     *
     * @return bool | array
     */
    public function getConveadVisitorData() {
        $permissions = permissionsCollection::getInstance();

        if(!$permissions->isAuth()) {
            return false;
        }

        $userId = $permissions->getUserId();

        $user = umiObjectsCollection::getInstance()->getObject($userId);

        $data = array(
            'visitor_uid' => $userId,
            'visitor_info' => $this->getConveadVisitorInfo($user)
        );

        return $data;
    }

    public function getConveadVisitorInfo(umiObject $user) {
        $birthday = $user->getValue('birthday');

        $raw = array(
            'first_name' => $user->getValue('fname'),
            'last_name' => $user->getValue('lname'),
            'email' => ($user->getTypeGUID() == 'users-user') ? $user->getValue('e-mail') : $user->getValue('email'),
            'phone' => $user->getValue('phone'),
            'date_of_birth' => ($birthday instanceof umiDate) ? date('Y-m-d', $birthday->getDateTimeStamp()) : false,
            'gender' => $user->getValue('gender')
        );

        $event = new umiEventPoint('convead-getVisitorInfo');
        $event->setParam('user', $user);
        $event->addRef('data', $raw);
        $event->call();

        $return = array();

        foreach($raw as $key => $value) {
            $value = trim($value);

            if($value != '') {
                $return[$key] = $value;
            }
        }

        unset($user);

        return $return;
    }

    public function getConveadViewProduct() {
        $raw = array('product_id' => false, 'category_id' => false, 'product_name' => false, 'product_url' => false);

        $controller = cmsController::getInstance();

        if($controller->getCurrentModule() == 'catalog' && $controller->getCurrentMethod() == 'object') {
            $hierarchy = umiHierarchy::getInstance();

            $productId = $controller->getCurrentElementId();

            $product = $hierarchy->getElement($productId);

            if($product) {
                $raw['product_id'] = $productId;
                $raw['category_id'] = $product->getParentId();
                $raw['product_name'] = $product->getName();
                $raw['product_url'] = $hierarchy->getPathById($productId);
            }
        }

        $event = new umiEventPoint('convead-viewProduct');
        $event->addRef('data', $raw);
        $event->call();

        $productId = getArrayKey($raw, 'product_id');

        if(!$productId) return false;

        $categoryId = getArrayKey($raw, 'category_id');

        if(!$categoryId) return false;

        $productName = getArrayKey($raw, 'product_name');

        if(!$productName) return false;

        $productUrl = getArrayKey($raw, 'product_url');

        if(!$productUrl) return false;

        return array(
            'product_id' => $productId,
            'category_id' => $categoryId,
            'product_name' => $productName,
            'product_url' => $productUrl
        );
    }

    /**
     * @return bool|ConveadTracker
     */

    public function getConveadTracker($visitorUid = false, $visitorInfo = false) {
        $key = $this->getConveadAppKey();

        if(!$key) return false;

        require_once 'api/ConveadTracker.php';

        $tracker = new ConveadTracker($key, $_SERVER['HTTP_HOST'], $_COOKIE['convead_guest_uid'], $visitorUid, $visitorInfo);

        return $tracker;
    }

    /**
     * @return bool|ConveadTracker
     */

    public function getConveadTrackerAnonym() {
        $key = $this->getConveadAppKey();

        if(!$key) return false;

        require_once 'api/ConveadTracker.php';

        $tracker = new ConveadTracker($key);

        return $tracker;
    }

    /**
     * @return state
     */
    public function switchState($state) {
        switch ($state) {
          case 'waiting':
            $state = 'new';
            break;
          case 'accepted':
            $state = 'paid';
            break;
          case 'ready':
            $state = 'shipped';
            break;
          case 'canceled':
            $state = 'canceled';
            break;
          case 'rejected':
            $state = 'canceled';
            break;
        }
        return $state;
    }

    /**
     * @return orderData
     */
    public function getOrderData($order) {
        if (!$order) return false;
        $orderData = new stdClass();
        $items = $order->getItems();
        $conveadItems = array();
        foreach($items as $item) {
            $conveadItems[] = array(
                'product_id' => $item->getItemElement()->getId(),
                'qnt' => $item->getAmount(),
                'price' => $item->getItemPrice(),
            );
        }
        $orderData->items = $conveadItems;
        $orderData->revenue = $order->getActualPrice();
        $orderData->order_id = $order->getValue('number');
        $orderData->state = $this->switchState( order::getCodeByStatus($order->getValue('status_id')) );
        return $orderData;
    }
    
    public function getConveadAppKey() {
        $regedit = regedit::getInstance();

        return $regedit->getVal('//modules/convead/app_key');
    }

};