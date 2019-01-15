<?php
namespace BoergenerWebdesign\BwDpsgList\Controller;
use \BoergenerWebdesign\BwDpsgList\Domain\Repository\MaillistRepository;

class MaillistController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var MaillistRepository
     */
    protected $maillistRepository;


    public function __construct(MaillistRepository $maillistRepository) {
    	$this -> maillistRepository = $maillistRepository;
    }

    public function listAction() {
        $maillists = $this->maillistRepository->findAll();
        $this->view->assign('maillists', $maillists);
    }

    public function showAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) {
    	$this -> view -> assignMultiple([
    		'maillist' => $maillist
	    ]);
    }

    public function checkAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) {
    	$client = \BoergenerWebdesign\BwDpsgList\Utilities\Mailman::create($maillist);
    	if($client -> login()) {
		    $this->addFlashMessage(
			    'Verbindung zur Mailman Liste erfolgreich hergestellt.'
		    );
	    } else {
		    $this->addFlashMessage(
			    'Es konnte keine Verbindung zur Mailman Liste hergestellt werden.',
			    '',
			    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
		    );
	    }
    	$this -> redirect("show", null, null, ["maillist" => $maillist]);
    }

	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist|null $maillist
	 */
    public function updateAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist = null) {
    	$maillists = $maillist ? [$maillist] : $this -> maillistRepository -> findAll();

	    /** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $curMaillist */
	    foreach($maillists as $curMaillist) {
		    try {
			    $client = \BoergenerWebdesign\BwDpsgList\Utilities\Mailman::create($curMaillist);
			    $client -> updateMembers();
			    $this->addFlashMessage(
				    "Mitglieder des Verteilers '".$curMaillist -> getName()."' wurde erfolgreich synchronisiert."
			    );
		    } catch (\Exception $e) {
			    $this->addFlashMessage(
				    $e -> getMessage(),
				    "Mitglieder des Verteilers '".$curMaillist -> getName()."' konnten nicht synchronisiert werden:",
				    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
			    );
		    }
	    }

	    if($maillist) {
		    $this -> redirect("show", null, null, ["maillist" => $maillist]);
	    } else {
		    $this -> redirect("list");
	    }
    }

	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist|null $maillist
	 */
    public function configAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist = null) : void {
	    $maillists = $maillist ? [$maillist] : $this -> maillistRepository -> findAll();

    	/** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $curMaillist */
	    foreach($maillists as $curMaillist) {
	    	try {
			    $client = \BoergenerWebdesign\BwDpsgList\Utilities\Mailman::create($curMaillist);
			    $client -> configList();
			    $this->addFlashMessage(
				    "Der Verteiler '".$curMaillist -> getName()."' wurde erfolgreich konfiguriert."
			    );
		    } catch (\Exception $e) {
			    $this->addFlashMessage(
				    $e -> getMessage(),
				    "Der Verteiler '".$curMaillist -> getName()."' konnte nicht konfiguriert werden:",
				    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
			    );
		    }
	    }

	    if($maillist) {
		    $this -> redirect("show", null, null, ["maillist" => $maillist]);
	    } else {
		    $this -> redirect("list");
	    }
    }
}
