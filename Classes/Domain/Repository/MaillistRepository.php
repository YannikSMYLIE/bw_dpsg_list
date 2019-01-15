<?php
/**
 * Created by PhpStorm.
 * User: fpyb
 * Date: 08.01.2018
 * Time: 10:58
 */
namespace BoergenerWebdesign\BwDpsgList\Domain\Repository;


class MaillistRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	protected $defaultOrderings = array(
		'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	);
}