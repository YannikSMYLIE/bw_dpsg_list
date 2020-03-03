<?php
namespace BoergenerWebdesign\BwDpsgList\Controller;

class ServerController extends Controller {
    /**
     * @var \BoergenerWebdesign\BwDpsgList\Domain\Repository\ServerRepository
     * @inject
     */
    protected $serverRepository = null;

    /**
     * Listet alle Server auf.
     */
    public function listAction() : void {
        $this -> view -> assignMultiple([
            'server' => $this -> serverRepository -> findAll()
        ]);
    }

    /**
     * Stellt ein Interface zum Erstellen eines neuen Servers bereit.
     */
    public function newAction() : void {
        $versionen = [];
        foreach($GLOBALS['TYPO3_CONF_VARS']["EXTCONF"]["bwdpsglist"]["mailman"]["versions"] as $serverClass) {
            $versionen[$serverClass] = $serverClass::getName();
        }

        $this -> view -> assignMultiple([
            'versions' => $versionen
        ]);
    }

    /**
     * Legt einen neuen Server an.
     * ToDo: Validator
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server
     */
    public function createAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server) : void {
        $this -> serverRepository -> add($server);
        $this->addFlashMessage('Der Server wurde angelegt.');
        $this -> redirect('list', 'Server');
    }

    /**
     * Stellt eine Maske zum Bearbeiten eines Servers zur Verfügung.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server
     */
    public function editAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server) : void {
        $versionen = [];
        foreach($GLOBALS['TYPO3_CONF_VARS']["EXTCONF"]["bwdpsglist"]["mailman"]["versions"] as $serverClass) {
            $versionen[$serverClass] = $serverClass::getName();
        }

        $this -> view -> assignMultiple([
            "server" => $server,
            'versions' => $versionen
        ]);
    }

    /**
     * Aktualisiert einen Server.
     * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server
     */
    public function updateAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server) : void {
        $this -> serverRepository -> update($server);
        $this->addFlashMessage('Der Server wurde aktualisiert.');
        $this -> redirect('list', 'Server');
    }

    public function deleteAction(\BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server) : void {
        $this -> serverRepository -> remove($server);
        $this->addFlashMessage('Der Server wurde entfernt.');
        $this -> redirect('list', 'Server');
    }
}
