<?php
namespace BoergenerWebdesign\BwDpsgList\Domain\Model;

class Server extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
	 * @var string
	 */
	protected $address = "";
	/**
	 * @return string
	 */
	public function getAddress() : string {
		return $this -> address;
	}
    /**
     * @param string $address
     */
	public function setAddress(string $address) : void {
	    $this -> address = $address;
    }

	/**
	 * @var string
	 */
	protected $version = "";
	/**
	 * @return int
	 */
	public function getVersion() : string {
		return $this -> version;
	}
    /**
     * @return string
     */
    public function getVersionDescription() : string {
        return $this -> version::getName();
    }
    /**
     * @param string $version
     */
	public function setVersion(string $version) : void {
	    $this -> version = $version;
    }

    /**
     * @var string
     */
    protected $creationPassword = "";
    /**
     * @return string
     */
    public function getCreationPassword(): string {
        return $this->creationPassword;
    }
    /**
     * @param string $creationPassword
     */
    public function setCreationPassword(string $creationPassword) : void {
        $this->creationPassword = $creationPassword;
    }
}
