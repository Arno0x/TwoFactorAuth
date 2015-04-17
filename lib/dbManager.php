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
    function __construct ($dbFilename, $flags = SQLITE3_OPEN_READWRITE) {
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
        $username = SQLite3::escapeString ($username);
        $passwordHash = hash('sha256',$password);
        
        // Prepare SQL query
        $sqlQuery = "INSERT INTO USERS (USERNAME ,PASSWORDHASH ,GAUTHSECRET ,ISADMIN) ";
        $sqlQuery .= "VALUES ('".$username."','".$passwordHash."','".$gauthSecret."',".$isAdmin.");";
        
        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Delete a user
    // @param username : The username
    // @return bool : TRUE if the user was deleted, FALSE otherwise
    public function deleteUser ($username) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        
        // Prepare SQL query
        $sqlQuery = "DELETE from USERS where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
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
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        
        // Prepare SQL query
        $sqlQuery = "SELECT PASSWORDHASH from USERS where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->querySingle($sqlQuery))) {
            return false;
        }
        else {
            return $ret;
        }
    }
    
    //--------------------------------------------------------
    // Get the Google Auth secret of a given user
    // @param username : The username
    // @return string : the Google Auth secret, or FALSE if there was an error
    public function getGauthSecret ($username) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        
        // Prepare SQL query
        $sqlQuery = "SELECT GAUTHSECRET from USERS where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->querySingle($sqlQuery))) {
            return false;
        }
        else {
            return $ret;
        }
    }
    
    //--------------------------------------------------------
    // Get admin status of a given user
    // @param username : The username
    // @return int : the admin status, or FALSE if there was an error
    public function getAdminStatus ($username) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        
        // Prepare SQL query
        $sqlQuery = "SELECT ISADMIN from USERS where USERNAME='".$username."';";

        // Perform SQL query
        if(($ret = $this->querySingle($sqlQuery)) === false) {
            return false;
        }
        else {
            return (int)$ret;
        }
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
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        
        // Prepare SQL query
        $sqlQuery = "SELECT PASSWORDHASH, GAUTHSECRET from USERS where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->querySingle($sqlQuery, true))) {
            return false;
        }
        else {
        	 return $ret;
        }
    }
    
    //--------------------------------------------------------
    // Updates the password hash of a user
    // @param username : the username
    // @param password; the new password for the user
    // @return bool : TRUE if the password was updated, FALSE otherwise
    public function updatePassword ($username, $password) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        $passwordHash = hash('sha256',$password);
        
        // Prepare SQL query
        $sqlQuery = "UPDATE USERS set PASSWORDHASH='".$passwordHash."' where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Updates the Google Auth secret of a user
    // @param username : the username
    // @param gauthSecret; the new Google Auth secret for the user
    // @return bool : TRUE if the Google Auth secret was updated, FALSE otherwise
    public function updateGauthSecret ($username, $gauthSecret) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);

        // Prepare SQL query
        $sqlQuery = "UPDATE USERS set GAUTHSECRET='".$gauthSecret."' where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
    
    //--------------------------------------------------------
    // Updates the admin status of a user
    // @param username : the username
    // @param isAdmin; the new admin status for the user
    // @return bool : TRUE if the admin status was updated, FALSE otherwise
    public function updateAdminStatus ($username, $isAdmin) {
        
        // Prepare variables before the query
        $username = SQLite3::escapeString ($username);
        if ($isAdmin !== 0 && $isAdmin !== 1) return false;

        // Prepare SQL query
        $sqlQuery = "UPDATE USERS set ISADMIN=".$isAdmin." where USERNAME='".$username."';";

        // Perform SQL query
        if(!($ret = $this->exec($sqlQuery))) {
            return false;
        }
        else {
            return true;
        }
    }
}
?>