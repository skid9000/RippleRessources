<?php
define('DATABASE_NAME', 'ripple');
define('DATABASE_USER', 'ripple');
define('DATABASE_PASS', 'SuchPasswordMuchWow');
define('DATABASE_HOST', 'localhost');
define('DATABASE_WHAT', 'host');

define('CREATE_ADMIN', 'true');
define('ADMIN_USERNAME', '');
define('ADMIN_EMAIL', '');

$db = new DBPDO();

function randomString($l) {
	$c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$res = '';
	srand((float) microtime() * 1000000);
	for ($i = 0; $i < $l; $i++) {
		$res .= $c[rand() % strlen($c)];
	}

	return $res;
}

echo "Fokabot doesn't exist. Creating account...\n";
$plainPassword = randomString(8);
$options = ['cost' => 9, 'salt' => base64_decode(base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)))];
$md5Password = crypt(md5($plainPassword), '$2y$'.$options['salt']);
$db->execute("INSERT INTO `users`(`id`, `username`, `username_safe`, `password_md5`, `salt`, `email`, `register_datetime`, `rank`, `allowed`, `latest_activity`, `silence_end`, `silence_reason`, `password_version`, `privileges`, `flags`) VALUES ('999', 'FokaBot', 'fokabot', ?, ?, 'fo@kab.ot', '1452544880', '4', '1', '0', '0', '', 1, 978427, 0);", [$md5Password, base64_encode($options['salt'])]);
$db->execute("INSERT INTO `users_stats`(`id`, `username`, `username_aka`, `user_color`, `user_style`, `ranked_score_std`, `playcount_std`, `total_score_std`, `replays_watched_std`, `ranked_score_taiko`, `playcount_taiko`, `total_score_taiko`, `replays_watched_taiko`, `ranked_score_ctb`, `playcount_ctb`, `total_score_ctb`, `replays_watched_ctb`, `ranked_score_mania`, `playcount_mania`, `total_score_mania`, `replays_watched_mania`, `total_hits_std`, `total_hits_taiko`, `total_hits_ctb`, `total_hits_mania`, `country`, `show_country`, `level_std`, `level_taiko`, `level_ctb`, `level_mania`, `avg_accuracy_std`, `avg_accuracy_taiko`, `avg_accuracy_ctb`, `avg_accuracy_mania`, `pp_std`, `pp_taiko`, `pp_ctb`, `pp_mania`, `badges_shown`, `safe_title`, `userpage_content`, `play_style`, `favourite_mode`) VALUES ('999', 'FokaBot', '', 'black', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'XX', '1', '0', '0', '0', '0', '0.000000000000', '0.000000000000', '0.000000000000', '0.000000000000', '0', '0', '0', '0', '3,4,11,0,0,0', '0', '', 0, 0);");
echo 'Fokabot account created! Password is: '.$plainPassword."\n";
echo 'Yepp... thats all we got for now. Some stuff might be missing as not everything is tested and set up correctly.';

if(ADMIN_USERNAME == "true") {
	echo "Creating account...\n";
	$plainPassword = randomString(8);
	$options = ['cost' => 9, 'salt' => base64_decode(base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)))];
	$md5Password = crypt(md5($plainPassword), '$2y$'.$options['salt']);
	$db->execute("INSERT INTO `users`(`id`, `username`, `username_safe`, `password_md5`, `salt`, `email`, `register_datetime`, `rank`, `allowed`, `latest_activity`, `silence_end`, `silence_reason`, `password_version`, `privileges`, `flags`) VALUES ('1000', ?, ?, ?, ?, ?, '1452544880', '4', '1', '0', '0', '', 1, 3145727, 0);", [ADMIN_USERNAME,strtolower(ADMIN_USERNAME),ADMIN_EMAIL,$md5Password, base64_encode($options['salt'])]);
	$db->execute("INSERT INTO `users_stats`(`id`, `username`, `username_aka`, `user_color`, `user_style`, `ranked_score_std`, `playcount_std`, `total_score_std`, `replays_watched_std`, `ranked_score_taiko`, `playcount_taiko`, `total_score_taiko`, `replays_watched_taiko`, `ranked_score_ctb`, `playcount_ctb`, `total_score_ctb`, `replays_watched_ctb`, `ranked_score_mania`, `playcount_mania`, `total_score_mania`, `replays_watched_mania`, `total_hits_std`, `total_hits_taiko`, `total_hits_ctb`, `total_hits_mania`, `country`, `show_country`, `level_std`, `level_taiko`, `level_ctb`, `level_mania`, `avg_accuracy_std`, `avg_accuracy_taiko`, `avg_accuracy_ctb`, `avg_accuracy_mania`, `pp_std`, `pp_taiko`, `pp_ctb`, `pp_mania`, `badges_shown`, `safe_title`, `userpage_content`, `play_style`, `favourite_mode`) VALUES ('1000', ?, '', 'black', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'XX', '1', '1', '1', '1', '1', '0.000000000000', '0.000000000000', '0.000000000000', '0.000000000000', '0', '0', '0', '0', '1,0,0,0,0,0', '0', '', 0, 0);", [ADMIN_USERNAME]);
	echo 'New account created! Password is: '.$plainPassword."\n";
}

class DBPDO {
	public $pdo;
	private $error;

	public function __construct() {
		$this->connect();
	}

	public function prep_query($query) {
		return $this->pdo->prepare($query);
	}

	public function connect() {
		if (!$this->pdo) {
			$dsn = 'mysql:dbname='.DATABASE_NAME.';'.DATABASE_WHAT.'='.DATABASE_HOST;
			$user = DATABASE_USER;
			$password = DATABASE_PASS;
			try {
				$this->pdo = new PDO($dsn, $user, $password, [PDO::ATTR_PERSISTENT => true]);

				return true;
			}
			catch(PDOException $e) {
				$this->error = $e->getMessage();
				die($this->error);

				return false;
			}
		} else {
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

			return true;
		}
	}

	public function table_exists($table_name) {
		$stmt = $this->prep_query('SHOW TABLES LIKE ?');
		$stmt->execute([$this->add_table_prefix($table_name)]);

		return $stmt->rowCount() > 0;
	}

	public function execute($query, $values = null) {
		if ($values === null) {
			$values = [];
		} elseif (!is_array($values)) {
			$values = [$values];
		}
		$stmt = $this->prep_query($query);
		$stmt->execute($values);

		return $stmt;
	}

	public function fetch($query, $values = null) {
		if ($values === null) {
			$values = [];
		} elseif (!is_array($values)) {
			$values = [$values];
		}
		$stmt = $this->execute($query, $values);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchAll($query, $values = null, $key = null) {
		if ($values === null) {
			$values = [];
		} elseif (!is_array($values)) {
			$values = [$values];
		}
		$stmt = $this->execute($query, $values);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		// Allows the user to retrieve results using a
		// column from the results as a key for the array
		if ($key != null && $results[0][$key]) {
			$keyed_results = [];
			foreach ($results as $result) {
				$keyed_results[$result[$key]] = $result;
			}
			$results = $keyed_results;
		}

		return $results;
	}

	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
}
?>
