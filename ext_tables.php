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
	                'Maillist' => 'list,show,check,update,config'
                ],
                [
                    'access' => 'user,group',
					'icon'   => 'EXT:' . $extKey . '/Resources/Public/Icons/Modules/inspect.jpg',
                    'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_inspect.xlf',
                ]
            );

        }


        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwdpsglist_domain_model_logrecord', 'EXT:bw_dpsg_list/Resources/Private/Language/locallang_csh_tx_bwdpsglist_domain_model_logrecord.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwdpsglist_domain_model_logrecord');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwdpsglist_domain_model_forwardrule', 'EXT:bw_dpsg_list/Resources/Private/Language/locallang_csh_tx_bwdpsglist_domain_model_forwardrule.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwdpsglist_domain_model_forwardrule');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwdpsglist_domain_model_mail', 'EXT:bw_dpsg_list/Resources/Private/Language/locallang_csh_tx_bwdpsglist_domain_model_mail.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwdpsglist_domain_model_mail');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwdpsglist_domain_model_imap', 'EXT:bw_dpsg_list/Resources/Private/Language/locallang_csh_tx_bwdpsglist_domain_model_imap.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwdpsglist_domain_model_imap');

    },
    $_EXTKEY
);
