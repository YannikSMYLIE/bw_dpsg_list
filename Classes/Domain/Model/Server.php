<?php
namespace BoergenerWebdesign\BwDpsgList\Domain\Model;

class Server extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{
	/**
	 * @var string
	 */
	protected $address;
	/**
	 * @return string
	 */
	public function getAddress() : string {
		return $this -> address;
	}

	/**
	 * @var int
	 */
	protected $version;
	/**
	 * @return /**
	 */
	public function getVersion() : int {
		return $this -> version;
	}

}
