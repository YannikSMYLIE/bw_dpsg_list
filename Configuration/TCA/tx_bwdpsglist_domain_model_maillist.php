<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
	    'default_sortby' => 'name ASC',
		'enablecolumns' => [
        ],
		'searchFields' => 'server,name',
        'iconfile' => 'EXT:bw_dpsg_list/Resources/Public/Icons/Models/tx_bwdpsglist_domain_model_maillist.svg'
    ],
    'interface' => [
		'showRecordFieldList' => 'server,name',
    ],
	'palettes' => [
		'access' => [
			'showitem' => 'server,name,password'
		],
		'list' => [
			'showitem' => 'type,archive,listowner'
		]
	],
    'types' => [
		'1' => ['showitem' => '
		--palette--;LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.palettes.access;access,
		--palette--;LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.palettes.list;list,
		senders,
		receivers
		'],
    ],
    'columns' => [
        'server' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.server',
	        'config' => [
		        'type' => 'select',
		        'renderType' => 'selectSingle',
		        'foreign_table' => 'tx_bwdpsglist_domain_model_server',
		        'foreign_table_where' => 'ORDER BY address',
			],
	    ],
        'name' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.name',
	        'config' => [
		        'type' => 'input',
		        'size' => 30,
		        'eval' => 'trim,required'
	        ],
        ],
        'password' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.password',
	        'config' => [
		        'type' => 'input',
		        'size' => 30,
		        'eval' => 'password,required'
	        ],
        ],
        'listowner' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.listowner',
	        'config' => [
		        'type' => 'input',
		        'size' => 60,
		        'eval' => 'trim,required,email'
	        ],
        ],
        'senders' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.senders',
	        'displayCond' => 'FIELD:type:=:0',
	        'config' => [
		        'type' => 'inline',
		        'foreign_table' => 'tx_bwdpsglist_domain_model_group',
		        'foreign_field' => 'maillist',
		        'foreign_match_fields' => [
			        'role' => 'sender',
		        ],
		        'appearance' => [
			        'collapseAll' => 0,
			        'expandSingle' => 1,
		        ],
	        ],
        ],
        'receivers' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.receivers',
	        'config' => [
		        'type' => 'inline',
		        'foreign_table' => 'tx_bwdpsglist_domain_model_group',
		        'foreign_field' => 'maillist',
		        'foreign_match_fields' => [
			        'role' => 'receiver',
		        ],
		        'appearance' => [
			        'collapseAll' => 0,
			        'expandSingle' => 1,
		        ],
	        ],
        ],
        'type' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.type',
	        'onChange' => 'reload',
	        'config' => [
		        'type' => 'select',
		        'renderType' => 'selectSingle',
		        'items' => [
			        ['LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.type.restricted', 0],
			        ['LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.type.all', 1],
		        ],
	        ],
        ],
        'archive' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.archive',
	        'config' => [
		        'type' => 'check',
		        'items' => [
			        [ 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_maillist.archive.1', 1 ],
		        ],
	        ],
        ],
    ],
];
