<?php
final class ConnectionFactory {

	private static $cache = array();

	private function __construct() {
		// Prevents the class from being instantiated
	}

	/**
	 * Creates DB connection.
	 * @return PDO
	 */
	static function getConnection($env = null) {
		global $connection;

		// Sets $def_cred->env from config.php as default environment
		if (empty($env)) {
			global $def_cred->env;
			$env = $def_cred->env;
		}

		// Does not create connection if it was already created
		if (empty(self::$cache[$env])) {
			$config = $connection[$env];

			if (empty($config)) {
				throw new Exception($env . ' is not a valid connection environment.');
			}

			// Create PDO Object
			$host = $config['host'];
			$db = $config['db'];
			$user = $config['user'];
			$pass = $config['pass'];
			$connection = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

			// Sets UTF-8 as used encoding
			$connection->exec("SET NAMES 'utf8'");
			$connection->exec('SET character_set_connection=utf8');
			$connection->exec('SET character_set_client=utf8');
			$connection->exec('SET character_set_results=utf8');

			// Throws an exception in case of SQL error
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Stores connection in cache
			self::$cache[$env] = $connection;
		}

		return self::$cache[$env];
	}

}
?>
