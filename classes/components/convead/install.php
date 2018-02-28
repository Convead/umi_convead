<?php

$INFO = [
		'name' => 'convead',
		'version' => "1.3.0.0",
		'config' => '1',
		'default_method/guest' => 'convead',
		'default_method_admin' => 'config'
	];

$moduleDir = "./classes/components/convead/";

$COMPONENTS = [
    $moduleDir . "admin.php",
    $moduleDir . "convead.php",
    $moduleDir . "customMacros.php",
    $moduleDir . "customAdmin.php",
    $moduleDir . "events.php",
    $moduleDir . "class.php",
    $moduleDir . "events.php",
    $moduleDir . "i18n.php",
    $moduleDir . "lang.php",
    $moduleDir . "permissions.php",
];

if(class_exists('permissionsCollection')) {
    $permissions = permissionsCollection::getInstance();
    $permissions->setModulesPermissions($permissions->getGuestId(), 'convead', 'convead');
}
