<?php

abstract class __convead extends baseModuleAdmin
{
    public function config() {
        self::setGuestPermissions();

        $mode = (string) getRequest('param0');

        $params = Array (
            "convead_config" => Array (
                "string:api_key" => '',
            )
        );

        if ($mode == "do") {
            $params = $this->expectParams($params);

            $this->setConveadApiKey($params['convead_config']['string:api_key']);

            $this->chooseRedirect();
        }

        $params['convead_config']['string:api_key'] = $this->getConveadApiKey();

        $this->setDataType("settings");
        $this->setActionType("modify");

        $data = $this->prepareData($params, "settings");

        $this->setData($data);
        return $this->doData();
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
