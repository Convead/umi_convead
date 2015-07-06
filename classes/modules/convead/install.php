<?php
$INFO = Array();

$INFO['version'] = "1.1.0.0";

$INFO['name'] = "convead";
$INFO['filename'] = "modules/convead/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_convead";
$INFO['default_method'] = "convead";
$INFO['default_method_admin'] = "config";

$SQL_INSTALL = Array();

$moduleDir = "./classes/modules/convead";

$COMPONENTS = array(
    $moduleDir . "/__admin.php",
    $moduleDir . "/__convead.php",
    $moduleDir . "/__custom.php",
    $moduleDir . "/__custom_adm.php",
    $moduleDir . "/__events.php",
    $moduleDir . "/class.php",
    $moduleDir . "/events.php",
    $moduleDir . "/i18n.php",
    $moduleDir . "/lang.php",
    $moduleDir . "/permissions.php",
);

if(class_exists('permissionsCollection')) {
    $permissions = permissionsCollection::getInstance();
    $permissions->setModulesPermissions($permissions->getGuestId(), 'convead', 'convead');
}