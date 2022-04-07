<?php 
// +------------------------------------------------------------------------+
// | @author        : Michael Arawole (Logad Networks)
// | @author_url    : https://logad.net
// | @author_email  : logadscripts@gmail.com
// | @date          : 22 Mar, 2022 09:38PM
// +------------------------------------------------------------------------+
// | Copyright (c) 2022 Logad Networks. All rights reserved.
// +------------------------------------------------------------------------+

class DB {
	## PDO Connection ##
	private static function PDOConnection() {
		require 'config.php';
		try {
			$pdo = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . ';charset=utf8', $dbuser, $dbpass);
			return $pdo;
		} catch (PDOException $exception) {
			exit('Failed to connect to database!');
		}
		return false;
	}

	## Run Query ##
	public static function RunQuery($data) {
		// $result = array();

		// Create connection
		$pdoConnection = self::PDOConnection();
		$query = $pdoConnection->prepare($data['query']);
		if (!empty($data['values'])) {
			$query->execute($data['values']);
		} else {
			$query->execute();

		}

		// Confirm Query
		if (!empty($data['returnConfirmation'])) {
			if ($query->rowCount() > 0) {
				return true;
			} else {
				return 500;
			}
		}

		// Fetch Results
		if (!empty($data['singleRecord'])) {
			$result = $query->fetch(PDO::FETCH_OBJ);
			return $result;
		} else {
			$result = $query->fetchAll(PDO::FETCH_OBJ);
			return $result;
		}

		return false;
	}
}

class QrPay {
	public static $defaultErrorMsg = "An error occurred";
	public static $currency = "₦";

	## Clean String ##
	public static function clean($string) {
		if (is_array($string) || is_object($string)) {
			return $string;
		}
		return htmlentities($string);
	}

	## Generate random string ##
	public static function GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false, $useZero = true) {
		$charset = '';
		if ($uselower) {
			$charset .= "abcdefghijklmnopqrstuvwxyz";
		}
		if ($useupper) {
			$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		if ($usenumbers) {
			if ($useZero) {
				$charset.= "0";
			}
			$charset .= "123456789";
		}
		if ($usespecial) {
			$charset .= "~@#$%^*()_+-={}|][";
		}
		if ($minlength > $maxlength) {
			$length = mt_rand($maxlength, $minlength);
		} else {
			$length = mt_rand($minlength, $maxlength);
		}
		$key = '';
		for ($i = 0; $i < $length; $i++) {
			$key .= $charset[(mt_rand(0, strlen($charset) - 1))];
		}
		return $key;
	}

	## Format amounts ##
	public static function amountFormat($amount, $addCurrency = false) {
		// if (is_float($amount)) {
		// 	return number_format($amount, 2);
		// }
		if ($addCurrency) {
			return self::$currency.number_format($amount, 2);
		} else {
			return number_format($amount, 2);
		}
	}

	public static function dateFormat($date, $customFormat = false) {
		if (!empty($customFormat)) {
			return date($customFormat, $date);
		}
		return date('d M, Y', $date);
	}
}

class Transactions extends QrPay {
	// New Transaction
	public static function NewTransaction ($details) {
		$response['status'] = false;
		$response['msg'] = QrPay::$defaultErrorMsg;

		$transId = date('ymd').QrPay::GenerateKey(4, 4, false, false);
		$query = DB::RunQuery([
			"query" => "INSERT INTO transactions (senderId, transId, type, amount, description, receiverId, date) VALUES (?, ?, ?, ?, ?, ?, ?)",
			"values" => [$details['senderId'], $transId, $details['type'], $details['amount'], $details['description'], $details['receiverId'], time()],
			"returnConfirmation" => true
		]);
		return $query;
	}

	public static function SendMoney($data) {
		$response['status'] = false;
		$response['msg'] = QrPay::$defaultErrorMsg;
		$data->amount = preg_replace("/[^0-9.]/", "", $data->amount);

		// Validate recipient uid
		$receiver = Users::ByUid($data->recipientUid);
		if (empty($receiver)) {
			$response['msg'] = "Invalid recipient";
			return $response;
		}

		if ($receiver->id == $data->sender_id) {
			$response['msg'] = "You can't send funds to yourself 😑";
			return $response;
		}

		// Check Balance
		$sender = Users::GetDetails($data->sender_id, true);
		if ($data->amount > $sender->balance) {
			$response['msg'] = "Insufficient balance";
			return $response;
		}

		// Credit Receiver and Debit Sender
		$settleBalance = Users::debitUserThenCreditAnotherUser($sender->id, $receiver->id, $data->amount);
		if ($settleBalance['status'] === true) {
			$request = Transactions::NewTransaction([
				"type" => "transfer",
				"senderId" => $sender->id,
				"receiverId" => $receiver->id,
				"amount" => $data->amount,
				"description" => "Transfer from $sender->uid to $data->recipientUid // ".$data->narration
			]);
			if ($request === true) {
				$response['status'] = true;
				$response['msg'] = "Transfer Successful";
			} else {
				$response['msg'] = "Transfer failed";
				// Rollback credit and debit
			}
		} else {
			$response['msg'] = $settleBalance['msg'];
		}

		return $response;
	}

	// Fetch Transactions
	public static function ByUser($user_id, $count = false) {
		if (!$count) {
			$data = array();
			$results = DB::RunQuery([
				"query" => "SELECT * FROM transactions WHERE senderId = ? OR receiverId = ? ORDER BY id desc",
				"values" => array($user_id, $user_id)
			]);
			foreach ($results as $obj) {
				$obj->amountFormatted = QrPay::amountFormat($obj->amount, true);
				$obj->dateFormatted = QrPay::dateFormat($obj->date);
				$obj->timeFormatted = QrPay::dateFormat($obj->date, 'h:ia');
				$obj->typeText = strtoupper(str_replace(array("-", "_"), null, $obj->type));
				if ($user_id == $obj->receiverId) {
					$obj->transType = "credit";
				}
				unset($obj->id, $obj->senderId, $obj->receiverId, $obj->amount, $obj->date);
				$data[] = $obj;
			}
			return $data;
		} else {
			$result = DB::RunQuery([
				"query" => "SELECT count(id) as total_count FROM transactions WHERE senderId = ? OR receiverId = ?",
				"values" => array($user_id, $user_id),
				"singleRecord" => true
			]);
			return $result->total_count;
		}

		return false;
	}
}

class Users extends Transactions {
	## Verify User Uid ##
	public static function ByUid($uid) {
		$result = DB::RunQuery([
			"query" => "SELECT id FROM users WHERE uid = ?",
			"values" => array($uid),
			"singleRecord" => true
		]);
		return $result;
	}

	## Credit User then debit another user ##
	protected static function debitUserThenCreditAnotherUser($debitUserId, $creditUserId, $debitAmount, $creditAmount = false) {
		$response['status'] = false;
		$response['msg'] = QrPay::$defaultErrorMsg;

		if (!$creditAmount) {
			$creditAmount = $debitAmount;
		}

		$debitQuery = DB::RunQuery([
			"query" => "UPDATE users SET balance = balance - ? WHERE id = ?",
			"values" => array($debitAmount, $debitUserId),
			"returnConfirmation" => true
		]);
		if ($debitQuery === true) {
			$creditQuery = DB::RunQuery([
				"query" => "UPDATE users SET balance = balance + ? WHERE id = ?",
				"values" => array($creditAmount, $creditUserId),
				"returnConfirmation" => true
			]);
			if ($creditQuery === true) {
				$response['status'] = true;
				$response['msg'] = "Success";
			} else {
				$response['msg'] = "Failed to credit user";
			}
		} else {
			$response['msg'] = "Failed to debit user";
		}
		return $response;
	}

	## Get User Details ##
	public static function GetDetails($user_id, $raw = false) {
		$data = array();
		$result = DB::RunQuery([
			"query" => "SELECT id, name, uid, balance, date FROM users WHERE id = ?",
			"values" => array($user_id),
			"singleRecord" => true
		]);
		if (!$raw) {
			$result->QrPayUid = $result->uid;
			$result->dateFormatted = QrPay::dateFormat($result->date);
			$result->balanceFormatted = QrPay::amountFormat($result->balance, true);
			$result->totalTransactions = Transactions::ByUser($result->id, true);
			unset($result->uid, $result->id, $result->date, $result->date, $result->balance);
		}
		return $result;
	}

	## Create User Account ##
	public static function Create($data) {
		$response['status'] = false;
		$response['msg'] = QrPay::$defaultErrorMsg;

		// Create UID
		$nameFormatted = trim(str_replace(" ", null, $data->name));
		$uid = strtoupper(substr($nameFormatted, 0, 2).substr($nameFormatted, -2).date("y").QrPay::GenerateKey(4, 4));
		$insert = DB::RunQuery([
			"query" => "INSERT INTO users (name, uid, balance, date) VALUES (?, ?, ?, ?)",
			"values" => array($data->name, $uid, 50000, time()),
			"returnConfirmation" => true
		]);
		if ($insert === true) {
			$response['status'] = true;
			$response['QrPayUid'] = $uid;
			$response['msg'] = "Account created successfully";
		}

		return $response;
	}
}