<?php
namespace BoergenerWebdesign\BwDpsgList\Domain\Model;

class Group extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{
	/**
	 * @var \BoergenerWebdesign\BwDpsgNami\Domain\Repository\MitgliedRepository
	 * @inject
	 */
	private $mitgliedRepository = null;

	public function __construct()
	{
		$this->additionalGroups = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @var bool
	 */
	protected $leaders;
	/**
	 * @return bool
	 */
	public function getLeaders() {
		return $this -> leaders;
	}

	/**
	 * @var bool
	 */
	protected $members;
	/**
	 * @return bool
	 */
	public function getMembers() {
		return $this -> members;
	}

	/**
	 * @var bool
	 */
	protected $staff;
	/**
	 * @return bool
	 */
	public function getStaff() {
		return $this -> staff;
	}

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup>
	 */
	protected $additionalGroups;
	/**
	 * @param \BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup $group
	 */
	public function addAdditionalGroups(\BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup $group) : void {
		$this -> additionalGroups -> attach($group);
	}
	/**
	 * @param \BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup $group
	 */
	public function removeAdditionalGroups(\BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup $group) : void {
		$this -> additionalGroups -> detach($group);
	}
	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup> $additionalGroups
	 */
	public function setAdditionalGroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $additionalGroups) : void {
		$this -> additionalGroups = $additionalGroups;
	}
	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BoergenerWebdesign\BwDpsgNami\Domain\Model\AdditionalGroup>
	 */
	public function getAdditionalGroups() {
		return $this -> additionalGroups;
	}

	/**
	 * @var string
	 */
	protected $stufen = "";
	/**
	 * @return string
	 */
	public function getStufen() : string {
		return $this -> stufen;
	}
	/**
	 * @param string $stufen
	 */
	public function setStufen(string $stufen) : void {
		$this -> stufen = $stufen;
	}

	public function getMails() {
		$stufenIds = $this -> getStufen() ? explode(",", $this -> getStufen()) : [];
		$groupIds = [];
		foreach($this -> getAdditionalGroups() as $additionalGroup) {
			$groupIds[] = $additionalGroup -> getUid();
		}
		$mitglieder = $this -> mitgliedRepository -> findByGroups($stufenIds, $groupIds, $this -> getLeaders(), $this -> getMembers(), $this -> getStaff());

		$emails = [];
		/** @var \BoergenerWebdesign\BwDpsgNami\Domain\Model\Mitglied $mitglied */
		foreach($mitglieder as $mitglied) {
			if($mitglied -> getEmailEltern()) {
				$emails[$mitglied -> getEmailEltern()] = [
					'name' => 'Eltern von '.$mitglied -> getName(),
					'mail' => $mitglied -> getEmailEltern()
				];
			}
			if($mitglied -> getEmailMitglied()) {
				$emails[$mitglied -> getEmailMitglied()] = [
					'name' => $mitglied -> getName(),
					'mail' => $mitglied -> getEmailMitglied()
				];
			}
		}

		$emails = array_values($emails);
		return $emails;
	}

}
