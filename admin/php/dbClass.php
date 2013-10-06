<?php

include("cryptography.php");
include("configs/dbconfig.php");

class dbClass
{
    /* Databas variabler */
    private $dbName, $dbUser, $dbPass, $dbHost, $connection;

    /* Variabler som hanterar användarkonton */
    private $hash, $username, $password, $email, $validate, $userid;

    /* Variabler som hanterar sessions */
    private $sessionVar, $sessionVal;

    /* Meddelande hantering */
    public $errors, $messages;

    /* När objektet skapas skapas också anslutningen till databasen */
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
            $this->errors = "Kunde inte ansluta. Följande sträng är den tekniska informationen: " . $e->getMessage();
            return false;
        }
        return true;
    }

    /* Registrering av t.ex. en användare */
    public function register($username, $password, $email)
    {
        if (!$this->connection)
        {
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();

        if ($checkRows > 0)
        {
            $this->errors = "Användarnamnet eller e-post adressen är tyvärr upptagen. Försök med en annan.";
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
            return false;
        }

        $this->messages = "Registreringen lyckades!";
        return $exec;
    }

    public function addGroup($groupName)
    {
        if (!$this->connection)
        {
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
            return false;
        }

        $this->messages = "The group was added successfully!";
        return $exec;
    }

    public function login($username, $password)
    {
        if (!$this->connection)
        {
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
        }

        $checkRows = $exec->rowCount();
        if (!$checkRows == 1)
        {
            $this->errors = "Fel användarnamn/lösenord";
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
            $this->messages = "Inloggningen lyckades.";

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
            $this->errors = "Inloggningen misslyckades. Fel användarnamn/lösenord";
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
                    $this->messages = "Användaren är redan inloggad";
                    return $_SESSION["userLoggedIn"];
                }
                else
                {
                    $this->errors = "Det ser ut som att någon är autentiserad men jag vet inte vem...";
                    session_destroy();
                    return false;
                }
            }
            else
            {
                $this->errors = "Användaren är inte autentiserad korrekt.";
                session_destroy();
                return false;
            }
        }
        else
        {
            $this->errors = "Användaren är inte inloggad. Var god logga in";
            return false;
        }
    }

    public function logout()
    {
        session_destroy();
        $this->messages = "Användaren loggades ut";
        return true;
    }

    public function changeUserPassword($oldpassword, $newpassword)
    {
        if (!$this->connection)
        {
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
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
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
                $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
            return false;
        }

        /* Get all users */

        $exec = $this->connection->prepare("
        SELECT *
        FROM `users`");

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
            return false;
        }

        /* Get all users */

        $exec = $this->connection->prepare("
        SELECT *
        FROM `groups`");

        try
        {
            $exec->execute();
        }
        catch(PDOException $e)
        {
            $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
            return false;
        }

        $pwLength = strlen($password);

        if ($pwLength = 0)
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
                $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
                $this->errors = "Någonting gick fel. Följande sträng är den tekniska informationen: " . $e->getMessage();
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
            $this->errors = "Ingen anslutning till databasen kunde hittas. Försök igen senare.";
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
}