<?php

/**
 * TwoFactorAuth database manager class - This class handles all interactions with
 * the user database. It extends the SQLite3 class.
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
class DBManager extends SQLite3 {
    
    //--------------------------------------------------------
    // Class constructor
    // @param dbFilename : The path to the database filename
    // @return bool : true if the database was properly opend, false otherwise
    function __construct ($dbFilename, $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE) {
    	    parent::__construct($dbFilename, $flags);
    }
    
    //--------------------------------------------------------
    // Adds a user to the database
    // @param username: the username to add
    // @param password: the user's password to add
    // @param gauthSecret: the user's Gauth secret to add
    // @param isAdmin: optionnal, specifies if the user is an admin. Defaults to 0. 
    // @return bool : TRUE if no error, FALSE otherwise
    public function addUser ($username, $password, $gauthSecret, $isAdmin = 0) {

		// Prepare variables before the query
		$passwordHash = hash('sha256',$password);
        
		// Prepare SQL query
		$sqlQuery = "INSERT INTO USERS (USERNAME ,PASSWORDHASH ,GAUTHSECRET ,ISADMIN) ";
		$sqlQuery .= "VALUES (:username, :passwordhash, :secret, :isadmin);";

		$stmt = $this->prepare($sqlQuery);

		if ($stmt) {
			$stmt->bindValue(':username', $username, SQLITE3_TEXT);
			$stmt->bindValue(':passwordhash', $passwordHash, SQLITE3_TEXT);
			$stmt->bindValue(':secret', $gauthSecret, SQLITE3_TEXT);
			$stmt->bindValue(':isadmin', $isAdmin, SQLITE3_INTEGER);

			if ($stmt->execute()) {
				return true;
			}
		}

		return false;
    }
    
    //--------------------------------------------------------
    // Delete a user
    // @param username : The username
    // @return bool : TRUE if the user was deleted, FALSE otherwise
    public function deleteUser ($username) {
        
		// Prepare SQL query
		$sqlQuery = "DELETE from USERS where USERNAME=:username;";

		$stmt = $this->prepare($sqlQuery);

		if ($stmt) {
			$stmt->bindValue(':username', $username, SQLITE3_TEXT);

			if ($stmt->execute()) {
				return true;
			}
		}

		return false;
    }
    
    //--------------------------------------------------------
    // Deletes all users from the table
    // @return bool : TRUE if all users were deleted, FALSE otherwise
    public function deleteAllUsers () {
        
        // Prepare SQL query
        $sqlQuery = "DELETE from USERS;";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Get the password hash of a given user
    // @param username : The username
    // @return string : the password hash, or FALSE if there was an error
    public function getPasswordHash ($username) {

		return $this->getUserData('PASSWORDHASH', $username);
    }

	//--------------------------------------------------------
	// Get a specific column of a given user
	// @param column : The column
	// @param username : The user's name
	// @return mixed
	protected function getUserData ($column, $username) {

		switch ($column) {
			case 'PASSWORDHASH':
			case 'GAUTHSECRET':
			case 'ISADMIN':
			case '*':
				break;

			default:
				return false;
		}

		$sqlQuery = "SELECT $column from USERS where USERNAME=:username;";

		$stmt = $this->prepare($sqlQuery);

		if ($stmt) {
			$stmt->bindValue(':username', $username, SQLITE3_TEXT);

			$res = $stmt->execute();
			$row = $res->fetchArray(SQLITE3_ASSOC);

			if ($row) {
				if ($column == '*') {
					return $row;
				}

				return $row["$column"];
			}
		}

		return false;
	}

    //--------------------------------------------------------
    // Get the Google Auth secret of a given user
    // @param username : The username
    // @return string : the Google Auth secret, or FALSE if there was an error
    public function getGauthSecret ($username) {

		return $this->getUserData('GAUTHSECRET', $username);
    }
    
    //--------------------------------------------------------
    // Get admin status of a given user
    // @param username : The username
    // @return int : the admin status, or FALSE if there was an error
    public function getAdminStatus ($username) {
        
		$status = $this->getUserData('ISADMIN', $username);

		if ($status) {
			return (int)$status;
		}

		return false;
    }
    
    //--------------------------------------------------------
    // Get the list of users in the database along with their admin status
    // @return array : an array of users/admin status
    public function getUserList () {
        
        // Prepare SQL query
        $sqlQuery = "SELECT USERNAME,ISADMIN from USERS;";

        // Perform SQL query
        if(!($ret = $this->query($sqlQuery))) {
            return false;
        }
        else {
        	$result = array();
        	while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        			$result[$row["USERNAME"]] = $row["ISADMIN"];
        	}
			return $result;
        }
    } 
    
    //--------------------------------------------------------
    // Get the password hash and the Google Auth secret of a given user
    // @param username : The username
    // @return array : the password hash and the Google Auth secret, or FALSE if there was an error
    public function getPasswordHashAndGauthSecret ($username) {

		return $this->getUserData('*', $username);
    }
    
    //--------------------------------------------------------
    // Updates the password hash of a user
    // @param username : the username
    // @param password; the new password for the user
    // @return bool : TRUE if the password was updated, FALSE otherwise
    public function updatePassword ($username, $password) {
        
        // Prepare variables before the query
        $passwordHash = hash('sha256',$password);
        
		return $this->updateUserData('PASSWORDHASH', $passwordHash, $username);
    }

	//--------------------------------------------------------
	// Get a specific column of a given user
	// @param column : The column
	// @param value : The updated value for column
	// @param username : The user's name
	// @return mixed
	protected function updateUserData ($column, $value, $username) {

		switch ($column) {
			case 'PASSWORDHASH':
			case 'GAUTHSECRET':
			case 'ISADMIN':
				break;

			default:
				return false;
		}

		$sqlQuery = "UPDATE USERS SET $column=:uservalue where USERNAME=:username;";

		$stmt = $this->prepare($sqlQuery);

		if ($stmt) {
			$stmt->bindValue(':uservalue', $value, ($column == 'ISADMIN' ? SQLITE3_INTEGER : SQLITE3_TEXT));
			$stmt->bindValue(':username', $username, SQLITE3_TEXT);

			if ($stmt->execute()) {
				return true;
			}
		}

		return false;
	}
    
    //--------------------------------------------------------
    // Updates the Google Auth secret of a user
    // @param username : the username
    // @param gauthSecret; the new Google Auth secret for the user
    // @return bool : TRUE if the Google Auth secret was updated, FALSE otherwise
    public function updateGauthSecret ($username, $gauthSecret) {

		return $this->updateUserData('GAUTHSECRET', $gauthSecret, $username);
    }
    
    //--------------------------------------------------------
    // Updates the admin status of a user
    // @param username : the username
    // @param isAdmin; the new admin status for the user
    // @return bool : TRUE if the admin status was updated, FALSE otherwise
    public function updateAdminStatus ($username, $isAdmin) {

		return $this->updateUserData('ISADMIN', $isAdmin, $username);
    }
}
?>
