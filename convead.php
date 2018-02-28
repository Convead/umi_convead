<?php
/**
 * Module configuration file for UMI exporter.
 * See http://api.docs.umi-cms.ru/razrabotka_nestandartnogo_funkcionala/umimarket/eksporter_modulya_dlya_umimarket/
 *
 */

return array(
    'package' => 'convead',
    'destination' => './convead',
    'directories' => array(
        './classes/components/convead',
        './styles/skins/mac/data/modules/convead'
    ),
    'files' => array(
        './images/cms/admin/mac/icons/big/convead.png',
        './images/cms/admin/mac/icons/medium/convead.png',
        './images/cms/admin/mac/icons/small/convead.png',
        './images/cms/admin/mac/icon/convead.png',
        './images/cms/admin/modern/icons/big/convead.png',
        './images/cms/admin/modern/icons/medium/convead.png',
        './images/cms/admin/modern/icons/small/convead.png',
        './images/cms/admin/modern/icon/convead.png',
        './man/ru/convead/config.html'
    ),
    'installScenario' => './classes/components/convead/install.php',
    'registry' => array(
        'convead' => array(
            'path' => 'modules/convead',
            'recursive' => true,
            'exclude' => array(
                'modules/convead/app_key',
                'modules/convead/permissions_set'
            )
        )
    )
);
?>