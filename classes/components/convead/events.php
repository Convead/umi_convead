<?php

new umiEventListener('order_refresh', 'convead', 'onOrderRefreshConveadUpdateCart');
new umiEventListener('order-status-changed', 'convead', 'onOrderStatusChangedConveadOrderState');
new umiEventListener('systemModifyObject', 'convead', 'onSystemModifyObjectConvead');
new umiEventListener('systemModifyPropertyValue', 'convead', 'onSystemModifyPropertyValueConvead');
