<?php
namespace BoergenerWebdesign\BwDpsgList\Utilities;

use mysql_xdevapi\Exception;

class Mailman {
	/**
	 * @param \BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist
	 * @return MailmanConnector\Mailman
	 * @throws \Exception
	 */
	public static function create(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) {
		switch($maillist -> getServer() -> getVersion()) {
			case 2: return new \BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector\Mailman2($maillist);
		}
		throw new \Exception("Mailman Connector für Version ".$maillist -> getServer() -> getVersion()." konnte nicht gefunden werden!");
	}
}