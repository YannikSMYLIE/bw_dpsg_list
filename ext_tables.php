<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        if (TYPO3_MODE === 'BE') {
        	if(!key_exists('dpsg', $GLOBALS['TBE_MODULES'])) {
		        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
			        'dpsg',
			        '',
			        'top',
			        null,
			        [
				        'access' => 'user,group',
				        'icon'   => 'EXT:bw_dpsg_nami/Resources/Public/Icons/Modules/dpsg.png',
				        'labels' => 'LLL:EXT:bw_dpsg_nami/Resources/Private/Language/locallang_dpsg.xlf',
			        ]
		        );
	        }

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'BoergenerWebdesign.BwDpsgList',
                'dpsg',
                'inspect',
                '',
                [
	                'Maillist' => 'list,new,create,edit,update,show,delete,config,synchronizeMembers',
	                'Server' => 'list,new,create,edit,update,delete'
                ],
                [
                    'access' => 'user,group',
					'icon'   => 'EXT:' . $extKey . '/Resources/Public/Icons/Modules/inspect.jpg',
                    'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_inspect.xlf',
                ]
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwdpsglist_domain_model_server');
    },
    $_EXTKEY
);
