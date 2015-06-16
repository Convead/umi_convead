<?php
/**
 * Module configuration file for UMI exporter.
 * See http://api.docs.umi-cms.ru/razrabotka_nestandartnogo_funkcionala/umimarket/eksporter_modulya_dlya_umimarket/
 *
 */

return array(
    'package' => 'convead',
    'modules' => array('convead'),
    'destination' => './convead',
    'directories' => array(
        './classes/modules/convead',
        './styles/skins/mac/data/modules/convead'
    ),
    'files' => array(
        './images/cms/admin/mac/icons/big/convead.png',
        './images/cms/admin/mac/icons/medium/convead.png',
        './images/cms/admin/mac/icons/small/convead.png',
        './man/ru/convead/config.html',
    ),
    'installScenario' => './classes/modules/convead/install.php',
    'registry' => array(
        'convead' => array(
            'path' => 'modules/convead',
            'recursive' => true,
            'exclude' => array(
                'modules/convead/api_key'
            )
        )
    )
);
?>
