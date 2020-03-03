<?php
namespace BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector;

class Mailman2 implements Mailman {
    public static function getName() : string {
        return "2.15.x";
    }


	private $guzzle = null;
	private $loggedIn = false;
	private $maillist = null;

	public function __construct(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist) {
		$this -> maillist = $maillist;

		// Guzzle initialisieren
		require_once(PATH_typo3conf."ext/bw_dpsg_list/Classes/Utilities/Guzzle/functions_include.php");
		$this -> guzzle = new \GuzzleHttp\Client([
			'cookies' => true,
			'base_uri' => 'https://'.$this -> maillist -> getServer() -> getAddress().'/mailman/admin/'.$this -> maillist -> getName().'/',
            'verify' => false
		]);
	}


    /**
     * Prüft, ob eine Verbindung zur Mailliste hergestellt werden kann und ob die angegebenen Zugangsdaten stimmen.
     * @return bool
     */
	public function login() : bool {
	    if($this -> loggedIn) {
	        return true;
        }

		try {
	        $this -> guzzle -> post("", [
				'form_params' => [
					'adminpw' => $this -> maillist -> getPassword(true)
				]
			]);
			$this -> loggedIn = true;
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}

    /**
     * Erstellt eine Liste
     * @return bool
     */
	public function create() : bool {
        try {
            $result = $this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/create', [
                'form_params' => [
                    'listname' => $this -> maillist -> getName(),
                    'owner' => $this -> maillist -> getListowner(),
                    'autogen' => 0,
                    'password' => $this -> maillist -> getPassword(),
                    'confirm' => $this -> maillist -> getPassword(),
                    'notify' => 1,
                    'auth' => $this -> maillist -> getServer() -> getCreationPassword(),
                    'doit' => 'Create List'
                ]
            ]);
            $this -> configList();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Entfernt die Liste vom Server.
     */
    public function delete() : void {
        $this -> loggedIn = false;
        $result = $this -> guzzle -> post('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/rmlist/'.$this -> maillist -> getName(), [
            'form_params' => [
                'password' => $this -> maillist -> getPassword(),
                'delarchives' => 1,
                'doit' => 'Delete this list'
            ]
        ]);
    }




















	public function getMembers() {
		if(!$this -> login()) {
			throw new \Exception("Es konnte keine Verbindung mit dem Verteiler hergestellt werden.");
		}
		$response = $this -> guzzle -> get('//'.$this -> maillist -> getServer() -> getAddress().'/mailman/admin/'.$this -> maillist -> getName().'/members');

		$dom = new \DOMDocument;
		$dom->loadHTML($response->getBody(), LIBXML_NOERROR);
		$table = $dom->getElementsByTagName('table')[4];
		$trs = $table -> getElementsByTagName("tr");

		$users = [];
		for($i = 2; $i < count($trs); $i++) {
			$tr = $trs[$i];
			$tds = $tr -> getElementsByTagName("td");
			$email = strtolower(trim($tds[1] -> getElementsByTagName("a")[0] -> textContent));
			$name = trim($tds[1] -> getElementsByTagName("input")[0] -> getAttribute('value'));
			$send = $tds[2] -> getElementsByTagName("input")[0] -> getAttribute('checked') != "checked";
			$receive = $tds[4] -> getElementsByTagName("input")[0] -> getAttribute('checked') != "checked";

			$users[] = [
				'email' => $email,
				'name' => $name,
				'send' => $send,
				'receive' => $receive
			];
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
		$emails = $this -> maillist -> getReceiversEmails();
		if($this -> maillist -> getType() == 0) {
			// Wenn nur bestimmte Personen an die Liste schreiben dürfen diese auch hinzufügen.
			$emails = array_merge($emails, $this -> maillist -> getSendersEmails());
		}

		foreach($emails as $email) {
			$index = $this -> findInMemberArray($email["mail"], $currentMembers);
			if($index !== false) {
				unset($currentMembers[$index]);
			} else {
				$addToMaillist[] = strtolower($email["mail"]);
			}
		}

        // Fehlende Mitglieder eintragen
		if($addToMaillist) {
			$this -> addUsers($addToMaillist);
		}
		// Gelöscht Mitglieder austragen
		if($currentMembers) {
			$emails = [];
			foreach($currentMembers as $currentMember) {
				$emails[] = $currentMember["email"];
			}
			$this -> removeUsers($emails);
		}

		// Berechtigungen aller Mitglieder setzen dazu die aktuellen Mitglieder erneut einlesen
		$currentMembers = $this -> getMembers();
		$members = [];
		foreach($currentMembers as $member) {
			$members[$member["email"]] = [
				"realname" => $member["name"],
				"nodupes" => 1,
				"plain" => 1,
				"language" => "en"
			];
			if(!$this -> maillist -> getType()) {
				$members[$member["email"]]["mod"] = 1;
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
				"moderator" => $this -> maillist -> getListowner(),
				"send_reminders" => 0,
                "admin_member_chunksize" => 7000,
				"max_message_size" => 0,
				"goodbye_msg" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/public/goodbyeack.txt")),
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
        // Passwort
        $this -> guzzle -> post('passwords/', [
            'form_params' => [
                "newpw" => $this -> maillist -> getPassword(),
                "confirmpw" => $this -> maillist -> getPassword(),
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
                "admin_member_chunksize" => 7000,
                "max_message_size" => 0,
				"goodbye_msg" => utf8_decode(file_get_contents(PATH_typo3conf."ext/bw_dpsg_list/Resources/Private/MailmanHTML/private/goodbyeack.txt")),
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
				"member_moderation_action" => 1,
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
		// Passwort
        $this -> guzzle -> post('passwords/', [
            'form_params' => [
                "newpw" => $this -> maillist -> getPassword(),
                "confirmpw" => $this -> maillist -> getPassword(),
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

	private function findInMemberArray($haystack, $array) {
		foreach($array as $index => $value) {
			if(strtolower($value["email"]) === strtolower($haystack)) {
				return $index;
			}
		}
		return false;
	}
}