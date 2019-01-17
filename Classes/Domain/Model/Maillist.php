<?php
namespace BoergenerWebdesign\BwDpsgList\Domain\Model;

class Maillist extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{
	public function __construct()
	{
		$this->senders = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->receivers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Server
	 */
    protected $server;
	/**
	 * @return \BoergenerWebdesign\BwDpsgList\Domain\Model\Server
	 */
    public function getServer() {
    	return $this -> server;
    }
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server
	 */
    public function setServer(\BoergenerWebdesign\BwDpsgList\Domain\Model\Server $server) : void {
		$this -> server = $server;
    }

    /**
     * @var string
     */
    protected $name;
    /**
     * @return string
     */
    public function getName() : string {
        return $this -> name;
    }
    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this -> name = $name;
    }

    /**
     * @var string
     */
    protected $displayname;
    /**
     * @return string
     */
    public function getDisplayname() : string {
        return $this -> displayname;
    }
    /**
     * @param string $displayname
     */
    public function setDisplayname(string $displayname) : void {
        $this -> displayname = $displayname;
    }

	/**
	 * @var string
	 */
    protected $password;
	/**
	 * @return string
	 */
	public function getPassword() : string {
		return $this -> password;
	}
	/**
	 * @param string $password
	 */
	public function setPassword(string $password) : void {
		$this -> password = $password;
	}

	/**
	 * @var string
	 */
	protected $listowner;
	/**
	 * @return string
	 */
	public function getListowner() : string {
		return $this -> listowner;
	}
	/**
	 * @param string $listowner
	 */
	public function setListowner(string $listowner) {
		$this -> listowner = $listowner;
	}

	/**
	 * @var int
	 */
	protected $type;
	/**
	 * @return int
	 */
	public function getType() : int {
		return $this -> type;
	}

	/**
	 * @var bool
	 */
	protected $archive;
	/**
	 * @return bool
	 */
	public function getArchive() : bool {
		return $this -> archive;
	}

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group>
	 */
    protected $senders;
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group
	 */
	public function addSenders(\BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group) : void {
		$this -> senders -> attach($group);
	}
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group
	 */
	public function removeSenders(\BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group) : void {
		$this -> senders -> detach($group);
	}
	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group> $senders
	 */
	public function setSenders(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $senders) : void {
		$this -> senders = $senders;
	}
	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group>
	 */
	public function getSenders() {
		return $this -> senders;
	}

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group>
	 */
    protected $receivers;
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group
	 */
	public function addReceivers(\BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group) : void {
		$this -> receivers -> attach($group);
	}
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group
	 */
	public function removeReceivers(\BoergenerWebdesign\BwDpsgList\Domain\Model\Group $group) : void {
		$this -> receivers -> detach($group);
	}
	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group> $receivers
	 */
	public function setReceivers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $receivers) : void {
		$this -> receivers = $receivers;
	}
	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgList\Domain\Model\Group>
	 */
	public function getReceivers() {
		return $this -> receivers;
	}

	public function getReceiversEmails() {
		return $this -> getEmails($this -> getReceivers());
	}
	public function getSendersEmails() {
		return $this -> getEmails($this -> getSenders());
	}
	private function getEmails($groups) {
		$emails = [];
		/** @var \BoergenerWebdesign\BwDpsgList\Domain\Model\Group $sender */
		foreach($groups as $member) {
			$emails = array_merge($emails, $member -> getMails());
		}
		return $emails;
	}
}
