<?php
new umiEventListener('order_refresh', 'convead', 'onOrderRefreshConveadUpdateCart');
new umiEventListener('order-status-changed', 'convead', 'onOrderStatusChangedConveadPurchase');