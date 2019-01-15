<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server',
        'label' => 'address',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
		'enablecolumns' => [
        ],
		'searchFields' => 'address',
        'iconfile' => 'EXT:bw_dpsg_list/Resources/Public/Icons/Models/tx_bwdpsglist_domain_model_server.svg'
    ],
    'interface' => [
		'showRecordFieldList' => 'address',
    ],
    'types' => [
		'1' => ['showitem' => 'address,version'],
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
		        'items' => [
		        	['LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_server.version.2', 2],
			        //['3.x', 3],
		        ]
	        ],
        ],
    ],
];
