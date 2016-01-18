<?php
class TTO {
	
	private static $userId = 0;
	private static $token = "default";
	private static $role  = "student";
	private static $email  = "";
	
	public static function getUserId() {
		return self::$userId;
	}
	public static function getToken() {
		return self::$token;
	}
	public static function getRole() {
		return self::$role;
	}
	public static function getEmail() {
		return self::$email;
	}
	public static function getUserEmail($userId) {
		$statement = 'SELECT email FROM user WHERE userId = :userId';
		$bind = array('userId' => $userId);
		return \Db::getValue($statement, $bind);
	}

	public static function setUserId($userId) {
		self::$userId = $userId;
	}
	public static function setToken($token) {
		self::$token = $token;
	}
	public static function setRole($role) {
		self::$role = $role;
	}
	public static function setEmail($email) {
		self::$email = $email;
	}
}
?>