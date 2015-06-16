<?php
	class convead extends def_module {
		public function __construct() {
			parent::__construct();

			$this->loadCommonExtension();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$configTabs = $this->getConfigTabs();

				if ($configTabs) {
					$configTabs->add("config");
				}

				$this->__loadLib("__admin.php");
				$this->__implement("__convead");

				$this->loadAdminExtension();

				// custom admin methods
				$this->__loadLib("__custom_adm.php");
				$this->__implement("__convead_custom_admin");
			}

			$this->loadSiteExtension();

			$this->__loadLib("__convead.php");
			$this->__implement("__convead_library");

			$this->__loadLib("__events.php");
			$this->__implement("__convead_events");

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_convead");
		}

		public function getConveadApiKey() {
			$regedit = regedit::getInstance();

			return $regedit->getVal('//modules/convead/api_key');
		}

		public function setConveadApiKey($key) {
			$regedit = regedit::getInstance();

			$regedit->setVar('//modules/convead/api_key', $key);
		}
	};
?>
