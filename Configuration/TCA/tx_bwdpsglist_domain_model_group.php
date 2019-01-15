<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
		'enablecolumns' => [
        ],
		'searchFields' => '',
        'iconfile' => 'EXT:bw_dpsg_list/Resources/Public/Icons/Models/tx_bwdpsglist_domain_model_group.svg'
    ],
    'interface' => [
		'showRecordFieldList' => '',
    ],
	'palettes' => [
		'type' => [
			'showitem' => 'leaders,members,staff'
		],
		'maillist' => [
			'showitem' => 'maillist,role'
		]
	],
    'types' => [
		'1' => ['showitem' => '
		--palette--;LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.palettes.type;type,
		stufen,
		additional_groups,
		--palette--;LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.palettes.maillist;maillist,
		'],
    ],
    'columns' => [
	    'leaders' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.leaders',
		    'config' => [
			    'type' => 'check',
			    'items' => [
				    [ '', 1 ],
			    ],
		    ],
	    ],
	    'members' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.members',
		    'config' => [
			    'type' => 'check',
			    'items' => [
				    [ '', 1 ],
			    ],
		    ],
	    ],
	    'staff' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.staff',
		    'config' => [
			    'type' => 'check',
			    'items' => [
				    [ '', 1 ],
			    ],
		    ],
	    ],
	    'stufen' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.stufen',
		    'config' => [
			    'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'items' => [
				    ['Wölflinge', 1],
				    ['Jungpfadfinder', 2],
				    ['Pfadfinder', 3],
				    ['Rover', 4],
			    ],
		    ],
	    ],
	    'additional_groups' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.additional_groups',
		    'config' => [
			    'type' => 'select',
			    'renderType' => 'selectMultipleSideBySide',
			    'foreign_table' => 'tx_bwdpsgnami_domain_model_additionalgroup',
			    'MM' => 'tx_bwdpsglist_groups_additionalgroups_mm',
			    'foreign_table_where' => 'ORDER BY name',
		    ],
	    ],
	    'maillist' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.maillist',
		    'config' => [
			    'type' => 'select',
			    'renderType' => 'selectSingle',
			    'foreign_table' => 'tx_bwdpsglist_domain_model_maillist',
			    'foreign_table_where' => 'ORDER BY name',
		    ],
	    ],
	    'role' => [
		    'exclude' => true,
		    'label' => 'LLL:EXT:bw_dpsg_list/Resources/Private/Language/locallang.xlf:tx_bwdpsglist_domain_model_group.role',
		    'config' => [
			    'type' => 'select',
			    'renderType' => 'selectSingle',
			    'items' => [
				    ['Absender', 'sender'],
				    ['Empfänger', 'receiver'],
			    ],
		    ],
	    ],
    ],
];
