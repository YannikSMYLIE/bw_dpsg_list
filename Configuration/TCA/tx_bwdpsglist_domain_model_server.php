<?php
$versionen = [];
foreach($GLOBALS['TYPO3_CONF_VARS']["EXTCONF"]["bwdpsglist"]["mailman"]["versions"] as $serverClass) {
    $versionen[] = [$serverClass::getName(), $serverClass];
}

return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server',
        'label' => 'address',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
		'enablecolumns' => [
        ],
		'searchFields' => 'address',
        'iconfile' => 'EXT:bw_dpsg_list/Resources/Public/Icons/Models/tx_bwdpsglist_domain_model_server.svg',
        'dividers2tabs' => true,
    ],
    'interface' => [
		'showRecordFieldList' => 'address',
    ],
    'types' => [
		'0' => ['showitem' => 'address,version,creation_passwort'],
    ],
    'columns' => [
        'address' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server.address',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim,required'
			],
	    ],
        'version' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server.version',
	        'config' => [
		        'type' => 'select',
		        'renderType' => 'selectSingle',
		        'items' => $versionen
	        ],
        ],
        'creation_password' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server.creation_password',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'password'
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
