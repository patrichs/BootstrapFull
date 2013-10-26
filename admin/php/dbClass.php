<?php

require_once("cryptography.php");
require_once("configs/dbconfig.php");

class dbClass
{
    /* Database vars */
    private $dbName, $dbUser, $dbPass, $dbHost, $connection;

    /* Handling user accounts */
    private $hash, $username, $password, $email, $validate, $userid;

    /* Handling sessions */
    private $sessionVar, $sessionVal;

    /* Message handling */
    public $errors, $messages;

    /* When you create the object we establish a connection to the database immediately. */
    public function __construct()
    {
        $getConfig = new dbconfig();

        $this->dbHost = $getConfig->databaseHost;
        $this->dbUser = $getConfig->databaseUsername;
        $this->dbPass = $getConfig->databasePassword;
        $this->dbName = $getConfig->databaseDatabase;

        try
        {
            $this->connection = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            $this->errors = "Cannot connect: " . $e->getMessage();
            return false;
        }
        return true;
    }

    /* Registrering av t.ex. en användare */
    public function register($username, $password, $email)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $this->hash = create_hash($password);
        $this->username = $username;
        $this->email = $email;

        /* Kolla om användarnamnet eller e-posten redan existerar */
        $data = array("username" => $this->username,
            "email" => $this->email);

        $exec = $this->connection->prepare("
        SELECT username, email
        FROM `users`
        WHERE username = :username
        OR email = :email
        ");

        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();

        if ($checkRows > 0)
        {
            $this->errors = "Username or email already taken. Please choose another.";
            return false;
        }

        /* Namngivna platshållare, påbörja inläggning av nytt konto */
        $data = array("username" => $this->username,
            "hash" => $this->hash,
            "email" => $this->email,
            "dateregistered" => date('Y-m-d H:i:s'));

        $exec = $this->connection->prepare("
        INSERT INTO `users` (username, hash, email, dateregistered)
        VALUE (:username, :hash, :email, :dateregistered)
        ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $this->messages = "Successfully created the new account!";
        return $exec;
    }

    public function addGroup($groupName)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        /* Check if group name exists */
        $data = array("groupname" => $groupName);

        $exec = $this->connection->prepare("
        SELECT groupname
        FROM `groups`
        WHERE groupname = :groupname
        ");

        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();

        if ($checkRows > 0)
        {
            $this->errors = "Group name already exists. Please use another name.";
            return false;
        }

        $exec = $this->connection->prepare("
        INSERT INTO `groups` (groupname)
        VALUE (:groupname)
        ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $this->messages = "The group was added successfully!";
        return $exec;
    }

    public function login($username, $password)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $this->username = $username;
        $this->password = $password;
        $data = array("username" => $this->username);

        $exec = $this->connection->prepare("
        SELECT userid, username, hash
        FROM `users`
        WHERE username = :username
        ");

        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();
        if (!$checkRows == 1)
        {
            $this->errors = "Wrong username/password";
            return false;
        }

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $this->hash = $row['hash'];
            $this->userid = $row["userid"];
        }

        $this->validate = validate_password($this->password, $this->hash);
        if ($this->validate)
        {
            session_start();
            $this->setSession("userAuthenticated", "Yes");
            $this->setSession("userLoggedIn", $this->username);
            $this->setSession("userIdLoggedIn", $this->userid);
            $this->messages = "Login successful.";

            $data = array("userid" => $this->userid,
            "logindate" => date('Y-m-d H:i:s'));

            $exec = $this->connection->prepare("
            UPDATE `users`
            SET lastlogin = :logindate
            WHERE userid = :userid");

            try
            {
                $exec->execute($data);
            }
            catch(PDOException $e)
            {
                $this->errors = "Could not insert lastlogin: " . $e->getMessage();
            }
        }
        else
        {
            $this->errors = "Login failed. Please check your credentials.";
        }
        return $this->validate;
    }

    public function setSession($sessionVar, $sessionVal)
    {
        $this->sessionVar = $sessionVar;
        $this->sessionVal = $sessionVal;

        return $_SESSION[$this->sessionVar] = $this->sessionVal;
    }

    public function checkAuthentication()
    {
        session_start();

        if (isset($_SESSION["userAuthenticated"]))
        {
            if ($_SESSION["userAuthenticated"] == "Yes")
            {
                if (isset($_SESSION["userLoggedIn"]))
                {
                    $this->messages = "User is already signed in.";
                    return true;
                }
                else
                {
                    $this->errors = "Something is authenticated but I don't know who.";
                    session_destroy();
                    return false;
                }
            }
            else
            {
                $this->errors = "The user is not authenticated correctly.";
                session_destroy();
                return false;
            }
        }
        else
        {
            $this->errors = "User not logged in. Please log in.";
            return false;
        }
    }

    public function logout()
    {
        session_destroy();
        $this->messages = "User was logged out successfully.";
        return true;
    }

    public function changeUserPassword($oldpassword, $newpassword)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $tmpUsername = $_SESSION["userLoggedIn"];

        $data = array("username" => $tmpUsername);

        $exec = $this->connection->prepare("
        SELECT username, hash
        FROM `users`
        WHERE userid = :username
        ");

        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();
        if (!$checkRows == 1)
        {
            $this->errors = "You are not authenticated properly. Pls 2 authenticate again";
            return false;
        }

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $this->hash = $row['hash'];
        }

        $this->validate = validate_password($oldpassword, $this->hash);
        if ($this->validate)
        {
            $data = array("username" => $tmpUsername,
                "hash" => create_hash($newpassword));

            $exec = $this->connection->prepare("
            UPDATE `users`
            SET hash = :hash
            WHERE userid = :username
            ");
            try
            {
                $exec->execute($data);
            }
            catch(PDOException $e)
            {
                $this->errors = "Something went wrong: " . $e->getMessage();
                return false;
            }

            $this->messages = "The password change was sucessful!";
            return true;
        }
        else
        {
            $this->errors = "The old password didn't match the one in our database. Did you write it correctly? Pls 2 try again.";
            return false;
        }
    }

    public function returnAllUsers()
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        /* Get all users */

        $exec = $this->connection->prepare("
        SELECT *
        FROM `users`
        LIMIT 10");

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $getArray = array();

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $getArray["amountOfRows"][] = array("userid" => $row["userid"],
                "username" => $row["username"],
                "email" => $row["email"],
            );
        }

        $amountOfUsers = $exec->rowCount();

        $getArray["amountOfUsers"] = $amountOfUsers;

        return json_encode($getArray);
    }

    public function returnAllGroups()
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        /* Get all users */

        $exec = $this->connection->prepare("
        SELECT *
        FROM `groups`
        LIMIT 10");

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $getArray = array();

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $getArray["amountOfRows"][] = array("groupid" => $row["groupid"],
                "groupname" => $row["groupname"]
            );
        }

        $amountOfGroups = $exec->rowCount();

        $getArray["amountOfGroups"] = $amountOfGroups;

        return json_encode($getArray);
    }

    public function changeUserData($userid, $username, $email, $password)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $pwLength = strlen($password);

        if ($pwLength == 0)
        {
            $data = array("username" => $username,
                "email" => $email,
                "userid" => $userid);

            $exec = $this->connection->prepare("
                UPDATE `users`
                SET username = :username, email = :email
                WHERE userid = :userid
                ");
            try
            {
                $exec->execute($data);
            }
            catch(PDOException $e)
            {
                $this->errors = "Something went wrong: " . $e->getMessage();
                return false;
            }

            $this->messages = "User updated successfully.";
            return true;
        }
        else
        {
            $data = array("username" => $username,
                "hash" => create_hash($password),
                "email" => $email,
                "userid" => $userid);

            $exec = $this->connection->prepare("
                UPDATE `users`
                SET username = :username, hash = :hash, email = :email
                WHERE userid = :userid
                ");
            try
            {
                $exec->execute($data);
            }
            catch(PDOException $e)
            {
                $this->errors = "Something went wrong: " . $e->getMessage();
                return false;
            }

            $this->messages = "User updated successfully!";
            return true;
        }
    }

    public function changeGroupData($groupid, $newgroupname)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $data = array("groupid" => $groupid,
            "groupname" => $newgroupname);

        $exec = $this->connection->prepare("
            UPDATE `groups`
            SET groupname = :groupname
            WHERE groupid = :groupid
            ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        $this->messages = "Group updated successfully!";
        return true;
    }

    public function adminDeleteUser($userId)
    {
        if (!$this->connection)
        {
            $this->errors = "Not database pls.";
            return false;
        }

        if (!isset($userId))
        {
            $this->errors = "No user id was supplied during the delete operation.";
            return false;
        }

        $data = array("userId" => $userId);

        $exec = $this->connection->prepare("
                DELETE FROM `users`
                WHERE userid = :userId
                ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $this->messages = "User deleted successfully!";
        return true;
    }

    public function adminDeleteGroup($groupId)
    {
        if (!$this->connection)
        {
            $this->errors = "Not database pls.";
            return false;
        }

        if (!isset($groupId))
        {
            $this->errors = "No group id was supplied during the delete operation.";
            return false;
        }

        $data = array("groupId" => $groupId);

        $exec = $this->connection->prepare("
                DELETE FROM `groups`
                WHERE groupId = :groupId
                ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $this->messages = "Group deleted successfully!";
        return true;
    }

    public function addUserToGroup($username, $groupid)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $data = array("groupid" => $groupid,
            "username" => $username);

        $exec = $this->connection->prepare("
            UPDATE `users`
            SET groupid = :groupid
            WHERE username = :username
            ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        $this->messages = $username . " was added to the group successfully!";
        return true;
    }

    public function addUserToGroupAutoComplete($username)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $data = array("username" => $username);

        $exec = $this->connection->prepare("
            SELECT username
            FROM `users`
            WHERE username LIKE CONCAT('%',:username,'%')
            LIMIT 5
            ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        $getArray = array();

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $getArray[] = array("value" => $row["username"]);
        }

        return $getArray;
    }

    public function graphUsersToday()
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $today = date("Y-m-d");
        $data = array("today" => $today);

        $exec = $this->connection->prepare("
            SELECT *
            FROM `users`
            WHERE dateregistered LIKE CONCAT('%',:today,'%')
            ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        $usersRegisteredToday = 0;
        $usersLoggedInToday = 0;

        while($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $usersRegisteredToday++;
        }

        $exec = $this->connection->prepare("
            SELECT *
            FROM `users`
            WHERE lastlogin LIKE CONCAT('%',:today,'%')
            ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        while($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $usersLoggedInToday++;
        }

        $getArray["RegisteredToday"] = $usersRegisteredToday;
        $getArray["LoggedInToday"] = $usersLoggedInToday;

        return $getArray;
    }

    public function writeToEventLog($eventid, $eventtitle, $eventdesc)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        $data = array("eventid" => $eventid,
            "eventtitle" => $eventtitle,
            "eventdesc" => $eventdesc,
            "eventdate" => date("Y-m-d H:i:s"));

        $exec = $this->connection->prepare("
        INSERT INTO `logs` (eventid, eventtitle, eventdesc, eventdate)
        VALUE (:eventid, :eventtitle, :eventdesc, :eventdate)
        ");
        try
        {
            $exec->execute($data);
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong. Here is the technical details: " . $e->getMessage();
            return false;
        }

        $this->messages = "Log was saved to the database.";
        return true;
    }

    public function returnLogsDashboard($limit)
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        /* Get logs */
        if ($limit == 1)
        {
            $exec = $this->connection->prepare("
            SELECT *
            FROM `logs`
            LIMIT 5
            ");
        }
        else
        {
            $exec = $this->connection->prepare("
            SELECT *
            FROM `logs`
            ");
        }

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $getArray = array();

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $getArray["amountOfRows"][] = array(
                "eventid"    => $row["eventid"],
                "eventtitle" => $row["eventtitle"],
                "eventdesc"  => $row["eventdesc"],
                "eventdate"  => $row["eventdate"]
            );
        }

        $amountOfLogs = $exec->rowCount();

        if ($limit == 1)
        {
            $getArray["amountOfLogs"] = $amountOfLogs;
        }

        return $getArray;
    }

    public function returnFullLogs()
    {
        if (!$this->connection)
        {
            $this->errors = "No connection to the database could be found. Try again later.";
            return false;
        }

        /* Get logs */

        $exec = $this->connection->prepare("
        SELECT *
        FROM `logs`
        ");

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Something went wrong: " . $e->getMessage();
            return false;
        }

        $getArray = array();

        while ($row = $exec->fetch(PDO::FETCH_ASSOC))
        {
            $getArray[] =
                $row["eventdate"] . " --- " .
                "ID: "    . $row["eventid"] . " --- " .
                " Title: " . $row["eventtitle"] . " --- " .
                " Description: "  . $row["eventdesc"] ;
        }

        return $getArray;
    }
}