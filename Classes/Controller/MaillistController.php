<?php
namespace BoergenerWebdesign\BwDpsgList\Controller;

class MaillistController extends Controller {
    /**
     * @var \BoergenerWebdesign\BwDpsgList\Domain\Repository\MaillistRepository
     * @inject
     */
    protected $maillistRepository = null;
    /**
     * @var \BoergenerWebdesign\BwDpsgList\Domain\Repository\ServerRepository
     * @inject
     */
    protected $serverRepository = null;

    /**
     * Zeigt eine Liste mit allen Maillisten an.
     */
    public function listAction() : void {
        $maillists = $this->maillistRepository->findAll();
        $server = [];
        /** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist */
        foreach($maillists as $maillist) {
            $server[$maillist -> getServer() -> getUid()][] = $maillist;
        }
        $this -> view -> assignMultiple([
            'server' => $server
        ]);
    }

    /**
     * Stellt eine Maske zur Verfügung, in der eine neue Mailliste angelegt werden kann.
     */
    public function newAction() : void {
        $this -> view -> assignMultiple([
            'server' => $this -> serverRepository -> findAll()
        ]);
    }

    /**
     * Legt eine neue Maillist an.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function createAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) : void {
        try {
            $this -> maillistRepository -> add($maillist);
            $this->addFlashMessage('Die Maillist wurde angelegt.');
        } catch(\Exception $e) {
            $this->addFlashMessage(
                $e -> getMessage(),
                'Die Mailliste konnte nicht angelegt werden:',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        $this -> redirect('list', 'Maillist');
    }

    /**
     * Stellt eine Maske zur Aktualisierung einer Maillist zur Verfügung.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function editAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) : void {
        $this -> view -> assignMultiple([
            'maillist' => $maillist,
            'server' => $this -> serverRepository -> findAll()
        ]);
    }

    /**
     * Aktualisiert eine Mailliste.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function updateAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) : void {
        try {
            $this -> maillistRepository -> update($maillist);
            $this->addFlashMessage('Die Maillist wurde aktualisiert.');
        } catch(\Exception $e) {
            $this->addFlashMessage(
                $e -> getMessage(),
                'Die Mailliste konnte nicht aktualisiert werden:',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        $this -> redirect('list', 'Maillist');
    }

    /**
     * Entfernt eine Mailliste
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function deleteAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) : void {
        try {
            $this -> maillistRepository -> remove($maillist);
            $this->addFlashMessage('Die Maillist wurde entfernt.');
        } catch(\Exception $e) {
            $this->addFlashMessage(
                $e -> getMessage(),
                'Die Mailliste konnte nicht entfernt werden:',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        $this -> redirect('list', 'Maillist');
    }

    /**
     * Zeigt eine einzelne Mailliste an.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
     */
    public function showAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) : void {
        /** @var \BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector\Mailman $client */
        $client = $maillist -> getClient();
        if(!$client -> login()) {
            $this->addFlashMessage(
                'Es konnte keine Verbindung zur Mailliste hergestellt werden.',
                'Kann Liste '.$maillist -> getName().' nicht öffnen:',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
            $this -> redirect('list');
        }
    	$this -> view -> assignMultiple([
    		'maillist' => $maillist
	    ]);
    }

	/**
     * Aktualisiert die Teilnehmer*innen einer Mailliste.
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist|null $maillist
	 */
    public function synchronizeMembersAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist = null) : void {
    	$maillists = $maillist ? [$maillist] : $this -> maillistRepository -> findAll();

	    /** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $curMaillist */
	    foreach($maillists as $curMaillist) {
		    try {
                $curMaillist -> getClient() -> updateMembers();
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
     * Konfiguriert eine Mailliste.
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist|null $maillist
	 */
    public function configAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist = null) : void {
	    $maillists = $maillist ? [$maillist] : $this -> maillistRepository -> findAll();

    	/** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $curMaillist */
	    foreach($maillists as $curMaillist) {
	    	try {
                $curMaillist -> getClient() -> configList();
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
