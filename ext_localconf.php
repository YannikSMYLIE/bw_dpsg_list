<?php
defined('TYPO3_MODE') || die('Access denied.');

// Unterstützte Versionen von Mailman
$GLOBALS['TYPO3_CONF_VARS']["EXTCONF"]["bwdpsglist"]["mailman"]["versions"] = [
    2 => \BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector\Mailman2::class
];

/*
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['BoergenerWebdesign\BwDpsgList\Task\OrganizeLists'] = array(
	'extension' => $_EXTKEY,
	'title' => 'Mitglieder synchronisieren',
	'description' => 'Synchronisiert Mitglieder aller hinterlegten Verteiler.'
);

if (TYPO3_MODE === 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']['BwDpsgList-Synchronize'] =
		\BoergenerWebdesign\BwDpsgList\Command\SynchronizeMembersCommandController::class;
}*/
?>