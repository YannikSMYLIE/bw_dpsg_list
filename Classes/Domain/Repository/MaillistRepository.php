<?php
namespace BoergenerWebdesign\BwDpsgList\Domain\Repository;

class MaillistRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	protected $defaultOrderings = array(
		'server' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
		'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	);

    /**
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
	public function add($maillist) : void {
	    if($maillist -> getClient() -> create()) {
            parent::add($maillist);
        } else {
	        throw new \Exception("Die Mailliste konnte nicht auf dem Mailman Server angelegt werden");
        }
    }

    /**
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function update($maillist) : void {
        if($maillist -> getClient() -> login()) {
            $maillist -> getClient() -> configList();
            parent::update($maillist);
        } else {
            throw new \Exception("Das eingegebene Passwort scheint nicht korrekt zu sein.");
        }
    }

    /**
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function remove($maillist) : void {
        if($maillist -> getClient() -> login()) {
            $maillist -> getClient() -> delete();
            parent::remove($maillist);
        } else {
            throw new \Exception("Das eingegebene Passwort scheint nicht korrekt zu sein.");
        }
    }
}