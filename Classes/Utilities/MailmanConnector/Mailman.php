<?php
namespace BoergenerWebdesign\BwDpsgList\Utilities\MailmanConnector;

interface Mailman {
	public function __construct(\BoergenerWebdesign\BwDpsgList\Domain\Model\Maillist $maillist);
    public static function getName() : string;

	public function login() : bool;
    public function create() : bool;
    public function delete() : void;
	public function getMembers();
	public function removeUser(string $email);
	public function removeUsers(array $emails);
	public function addUser(string $email);
	public function addUsers(array $emails);
	public function updateMembers();
	public function configList();
}