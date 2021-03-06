<?php

class ConveadAdmin  {

	use baseModuleAdmin;
	
	public $module;
	
    public function config() {
        $mode = (string) getRequest('param0');

        $params = Array (
            "convead_config" => Array (
                "string:app_key" => '',
            )
        );

        if ($mode == "do") {
            $params = $this->expectParams($params);

            $this->setConveadAppKey($params['convead_config']['string:app_key']);

            $this->chooseRedirect();
        }

        $params['convead_config']['string:app_key'] = $this->getConveadAppKey();

        $this->setDataType("settings");
        $this->setActionType("modify");

        $data = $this->prepareData($params, "settings");

        $this->setData($data);
        return $this->doData();
    }
    
	public function getConveadAppKey() {
		$regedit = regedit::getInstance();

		return $regedit->getVal('//modules/convead/app_key');
	}

    public function setConveadAppKey($key) {
        $regedit = regedit::getInstance();

        $regedit->setVar('//modules/convead/app_key', $key);
    }
    
};
