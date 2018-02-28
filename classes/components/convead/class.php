<?php

class convead extends def_module {

	public function __construct() {
		parent::__construct();

    	self::setGuestPermissions();

		$this->loadCommonExtension();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$configTabs = $this->getConfigTabs();

			if ($configTabs) $configTabs->add("config");

			$this->__loadLib("admin.php");
			$this->__implement("ConveadAdmin");

			$this->loadAdminExtension();

			// custom admin methods
			$this->__loadLib("customAdmin.php");
			$this->__implement("ConveadCustomAdmin");
		}

		$this->loadSiteExtension();

		$this->__loadLib("convead.php");
		$this->__implement("ConveadLibrary");

		$this->__loadLib("macros.php");
		$this->__implement("ConveadMacros");

		$this->__loadLib("customMacros.php");
		$this->__implement("ConveadCustomMacros");
	}

    public static function setGuestPermissions() {
        $regedit = regedit::getInstance();

        if(!$regedit->getVal('//modules/convead/permissions_set')) {
            $regedit->setVar('//modules/convead/permissions_set', true);

            $permissions = permissionsCollection::getInstance();
            $permissions->setModulesPermissions($permissions->getGuestId(), 'convead', 'convead');
        }
    }

};
?>
