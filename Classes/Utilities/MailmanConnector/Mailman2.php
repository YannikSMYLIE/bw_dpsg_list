<?php
namespace BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector;

class Mailman2 implements Mailman {
	private $guzzle = null;
	private $loggedIn = false;
	private $maillist = null;

	public function __construct(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) {
		$this -> maillist = $maillist;

		// Guzzle initialisieren
		require_once(PATH_typo3conf."ext/bw_dpsg_list/Classes/Utilities/Guzzle/functions_include.php");
		$this -> guzzle = new \GuzzleHttp\Client([
			'cookies' => true,
			'base_uri' => '//'.$this -> maillist -> getServer() -> getAddress().'/mailman/admin/'.$this -> maillist -> getName().'/'
		]);
	}

	public function login() {
		// Nicht zweimal anmelden
		if($this -> loggedIn) {
			return true;
		}

		try {
			$this -> guzzle -> post("", [
				'form_params' => [
					'adminpw' => $this -> maillist -> getPassword()
				]
			]);
			$this -> loggedIn = true;
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}

	public function getMembers() {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		$response = $this -> guzzle -> get('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/roster/'.$this -> maillist -> getName().'/');

		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody());
		$domElements = $dom->getElementsByTagName('li');
		$users = [];
		foreach($domElements as $user) {
			$mail = trim($user -> textContent);
			$match = [];
			if(preg_match ( '#\((.*?)\)#',$mail, $match)) {
				$users[] = $match[1];
			} else {
				$users[] = $mail;
			}
		}
		return $users;
	}

	public function removeUser(string $email) {
		$this -> removeUsers([$email]);
	}
	public function removeUsers(array $emails) {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		// Zuerst den CSRF Token ermitteln
		$csrfToken = false;
		$response = $this -> guzzle -> get('members/remove/');
		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody());
		foreach ($dom->getElementsByTagName('input') as $input)
		{
			if ($input->getAttribute('name') == 'csrf_token') {
				$csrfToken = $input->getAttribute('value');
			}
		}

		if(!$csrfToken) {
			return false;
		}

		// form_params?
		$this -> guzzle -> post('members/remove/', [
			'multipart' => [
				[
					'name'     => 'send_unsub_ack_to_this_batch',
					'contents' => 1
				],
				[
					'name'     => 'send_unsub_notifications_to_list_owner',
					'contents' => 0
				],
				[
					'name'     => 'unsubscribees',
					'contents' => implode("\n", $emails)
				],
				[
					'name'     => 'csrf_token',
					'contents' => $csrfToken
				],
			]
		]);
	}

	public function addUser(string $email) {
		$this -> addUsers([$email]);
	}
	public function addUsers(array $emails) {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		// Zuerst den CSRF Token ermitteln
		$csrfToken = false;
		$response = $this -> guzzle -> get('members/add/');
		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody());
		foreach ($dom->getElementsByTagName('input') as $input)
		{
			if ($input->getAttribute('name') == 'csrf_token') {
				$csrfToken = $input->getAttribute('value');
			}
		}

		if(!$csrfToken) {
			return false;
		}

		$this -> guzzle -> post('members/add/', [
			'form_params' => [
				'subscribe_or_invite' => 0,
				'send_welcome_msg_to_this_batch' => 1,
				'send_notifications_to_list_owner' => 0,
				'subscribees' => implode("\n", $emails),
				'csrf_token' => $csrfToken,
			]
		]);
	}

	public function updateMembers() {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		// Zuerst den CSRF Token ermitteln
		$response = $this -> guzzle -> get('members/list/');
		$csrf_token = false;
		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody());
		foreach ($dom->getElementsByTagName('input') as $input)
		{
			if ($input->getAttribute('name') == 'csrf_token') {
				$csrf_token = $input->getAttribute('value');
			}
		}
		if(!$csrf_token) {
			return false;
		}

		// Mitglieder einlsen
		// Aktuelle Mitglieder einlesen
		$currentMembers = $this -> getMembers();

		// Ermitteln wer noch kein Mitglied ist
		$addToMaillist = [];
		foreach($this -> maillist -> getReceiversEmails() as $receiversEmail) {
			$index = array_search($receiversEmail["mail"], $currentMembers);
			if($index !== false) {
				unset($currentMembers[$index]);
			} else {
				$addToMaillist[] = $receiversEmail["mail"];
			}
		}
		if($this -> maillist -> getType() == 0) {
			foreach($this -> maillist -> getSendersEmails() as $sendersEmail) {
				$index = array_search($sendersEmail["mail"], $currentMembers);
				if($index !== false) {
					unset($currentMembers[$index]);
				} else {
					$addToMaillist[] = $sendersEmail["mail"];
				}
			}
		}


		// Fehlende Mitglieder eintragen
		if($addToMaillist) {
			$this -> addUsers($addToMaillist);
		}
		// Gelöscht Mitglieder austragen
		if($currentMembers) {
			$this -> removeUsers($currentMembers);
		}

		// Berechtigungen aller Mitglieder setzen dazu die aktuellen Mitglieder erneut einlesen
		$currentMembers = $this -> getMembers();
		$members = [];
		foreach($currentMembers as $member) {
			$members[$member] = [
				"realname" => "",
				"nodupes" => 1,
				"plain" => 1,
				"language" => "en"
			];
			if(!$this -> maillist -> getType()) {
				$members[$member]["mod"] = 1;
			}
		}

		// Erst alle Sender einlesen und auf "nicht empfangen stellen" und mod auf "aus"
		// Nur wenn nicht alle Senden dürfen
		if(!$this -> maillist -> getType()) {
			foreach($this -> maillist -> getSendersEmails() as $sender) {
				$members[$sender["mail"]]["realname"] = utf8_decode($sender["name"]);
				$members[$sender["mail"]]["nomail"] = 1;
				unset($members[$sender["mail"]]["mod"]);
			}
		}

		// Dann alle Empfänger einlesen und auf "empfangen" stellen
		foreach($this -> maillist -> getReceiversEmails() as $receiver) {
			$members[$receiver["mail"]]["realname"] = utf8_decode($receiver["name"]);
			unset($members[$receiver["mail"]]["nomail"]);
		}

		
		// Variabeln speichern
		$vars = [];
		foreach($members as $mail => $settings) {
			$userVars = [];
			$userVars["user"] = urlencode($mail);
			foreach($settings as $index => $value) {
				$userVars[urlencode($mail)."_".$index] = $value;
			}
			$userVars["allmodbit_val"] = 0;
			$userVars["setmemberopts_btn"] = "Submit Your Changes";
			$userVars["csrf_token"] = $csrf_token;
			$vars[] = $userVars;
		}

		// Anfragen stellen
		foreach($vars as $userVars) {
			$resp = $this -> guzzle -> post('members/list/', [
				'form_params' => $userVars
			]);
		}
	}

	/**
	 * Konfiguriert einen Verteiler.
	 * @throws \Exception
	 */
	public function configList() {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		$csrfToken = $this -> getCSRFToken();
		if($csrfToken === false) {
			throw new \Exception("Es konnte kein CSRF-Token generiert werden.");
		}

		switch($this -> maillist -> getType()) {
			case 0: $this -> configPrivateList($csrfToken); break;
			case 1: $this -> configPublicList($csrfToken); break;
			default: throw new \Exception("Der Verteiler kann nicht auf den Typ ".$this -> maillist -> getType()." konfiguriert werden."); break;
		}
	}

	/**
	 * Konfiguriert eine Liste mit festen Empfänger*innen an die jeder schreiben kann.
	 * @param string $csrfToken
	 */
	private function configPublicList(string $csrfToken) : void {
		// General Einstellungen
		$this -> guzzle -> post('general/', [
			'form_params' => [
				"host_name" => "stamm-sugambrer.de",
				"owner" => "webmaster@stamm-sugambrer.de",
                "subject_prefix" => "[".utf8_decode($this -> maillist -> getDisplayname())."]",
				"moderator" => $this -> maillist -> getListowner(),
				"send_reminders" => 0,
				"goodbye_msg" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/goodbyeack.txt")),
				"admin_member_chunksize" => 9000,
                "max_message_size" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Diggest Einstellungen
		$this -> guzzle -> post('digest/', [
			'form_params' => [
				"digestable" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Archive Einstellungen
		$this -> guzzle -> post('archive/', [
			'form_params' => [
				"archive" => (int)$this -> maillist -> getArchive(),
				"archive_private" => 1,
				"csrf_token" => $csrfToken
			]
		]);
		// Privacy Options
		$this -> guzzle -> post('privacy/', [
			'form_params' => [
				"subscribe_policy" => 1,
				"private_roster" => 2,
				"obscure_addresses" => 0,
				"advertised" => 1,
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('privacy/sender/', [
			'form_params' => [
				"nonmember_rejection_notice" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/reject.txt")),
				"generic_nonmember_action" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('privacy/recipient/', [
			'form_params' => [
				"require_explicit_destination" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Public Pages
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/listinfo.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/listinfo.html"),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/subscribe.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/subscribe.html"),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/subscribeack.txt', [
			'form_params' => [
				"html_code" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/subscribeack.txt")),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/options.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/options.html"),
				"csrf_token" => $csrfToken
			]
		]);
	}

	/**
	 * Konfiguriert eine Liste mit festen Empfänger*innen und festen Absender*innen.
	 * @param string $csrfToken
	 */
	private function configPrivateList(string $csrfToken) : void {
		// General Einstellungen
		$this -> guzzle -> post('general/', [
			'form_params' => [
				"host_name" => "stamm-sugambrer.de",
				"owner" => "webmaster@stamm-sugambrer.de",
				"moderator" => $this -> maillist -> getListowner(),
				"send_reminders" => 0,
				"subject_prefix" => "[".utf8_decode($this -> maillist -> getDisplayname())."]",
				"goodbye_msg" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/goodbyeack.txt")),
                "admin_member_chunksize" => 9000,
                "max_message_size" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Diggest Einstellungen
		$this -> guzzle -> post('digest/', [
			'form_params' => [
				"digestable" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Archive Einstellungen
		$this -> guzzle -> post('archive/', [
			'form_params' => [
				"archive" => (int)$this -> maillist -> getArchive(),
				"archive_private" => 1,
				"csrf_token" => $csrfToken
			]
		]);
		// Privacy Options
		$this -> guzzle -> post('privacy/', [
			'form_params' => [
				"subscribe_policy" => 1,
				"private_roster" => 2,
				"obscure_addresses" => 0,
				"advertised" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('privacy/sender/', [
			'form_params' => [
				"member_moderation_notice" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/reject.txt")),
				"generic_nonmember_action" => 2,
				"nonmember_rejection_notice" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/reject.txt")),
				"forward_auto_discards" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('privacy/recipient/', [
			'form_params' => [
				"require_explicit_destination" => 0,
				"csrf_token" => $csrfToken
			]
		]);
		// Public Pages
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/listinfo.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/listinfo.html"),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/subscribe.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/subscribe.html"),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/subscribeack.txt', [
			'form_params' => [
				"html_code" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/subscribeack.txt")),
				"csrf_token" => $csrfToken
			]
		]);
		$this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/edithtml/'.$this -> maillist -> getName().'/options.html', [
			'form_params' => [
				"html_code" => file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/options.html"),
				"csrf_token" => $csrfToken
			]
		]);
	}

	private function getCSRFToken() {
		$response = $this -> guzzle -> get('members/list/');
		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody());
		foreach ($dom->getElementsByTagName('input') as $input)
		{
			if ($input->getAttribute('name') == 'csrf_token') {
				return $input->getAttribute('value');
			}
		}
		return false;
	}
}