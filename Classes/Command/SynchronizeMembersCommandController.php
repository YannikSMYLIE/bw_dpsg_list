<?php
namespace BoergenerWebdesign\BwDpsgList\Command;

class SynchronizeMembersCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * Synchronisiert eine oder mehrere Verteiler mit Mailman.
	 *
	 * Eine erweiterte Beschreibung.
	 *
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist Uid des Verteilers, falls leer werden alle Verteiler synchronisiert.
	 *
	 * @return bool
	 */
	public function synchronizeCommand($maillist = null)
	{
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \BoergenerWebdesign\BwDpsgList\Domain\Repository\MaillistRepository $maillistController */
		$maillistRepository = $objectManager->get('BoergenerWebdesign\\BwDpsgList\\Domain\\Repository\\MaillistRepository');
		$errors = [];
		$maillists = $maillist ? [$maillist] : $maillistRepository -> findAll();

		/** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $curMaillist */
		foreach($maillists as $curMaillist) {
			try {
				$client = \BoergenerWebdesign\BwDpsgList\Utilities\Mailman::create($curMaillist);
				$client -> updateMembers();
			} catch (\Exception $e) {
				$errors[] = "<p><b>Mitglieder des Verteilers '".$curMaillist -> getName()."' konnten nicht synchronisiert werden:</b><br>".$e -> getMessage()."</p>";
			}
		}

		if($errors) {
			echo implode("", $errors);
			return false;
		}
		return true;
	}
}