<?php

/*
  W większości podejść mamy następujące założenia:
  • Na każdą tablicę w naszej bazie danych przypada jedna klasa która ją reprezentuje.
  • Klasa ma taką samą nazwę jak tablica i atrybuty odpowiadające kolumnom tablicy.
  • Każdy obiekt tej klasy jest reprezentacją jednego rzędu z tablicy.
  • Obiekt może być w dwóch stanach: zsynchronizowany i niezsynchronizowany.
 */

//poniższe chyba jest niepotrzebne tutaj, po co w klasie połączenie z bazą danych?
//require_once 'connection.php';

class User {

    private $id;
    private $username;
    private $hashedPassword;
    private $email;

    public function __construct() {
        $this->id = -1;
        $this->username = '';
        $this->hashedPassword = '';
        $this->email = '';
    }

    public function setUsername($name) {
        $this->username = $name;
    }

    public function setHashedPassword($pass) {
        $newHashedPass = password_hash($pass, PASSWORD_BCRYPT);
        $this->hashedPassword = $newHashedPass;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->username;
    }

    public function getHashedPassword() {
        return $this->hashedPassword;
    }

    public function getEmail() {
        return $this->email;
    }

    public function saveToDB(mysqli $connection) {
        if ($this->id == -1) { //jeżeli obiek nie istnieje jeszcze w bazie danych
            $sql = "INSERT INTO User(email, username, hashed_password)"
                    . "Values ('$this->email', '$this->username', '$this->hashedPassword')";

            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = $connection->insert_id; //jeżeli udąło się zapisać obiekt do bazy to przypiszemy mu id
                return true;
            }
        } else {
            $sql = "UPDATE User SET username = '$this->username',"
                    . "email = '$this->email',"
                    . "hashed_password = '$this->hashedPassword'"
                    . "WHERE id = $this->id";

            $result = $connection->query($sql);
            if ($result == true) {
                return true;
            }
        }
        return false;
    }

    static public function loadUserById(mysqli $connection, $id) {
        $sql = "SELECT * FROM User WHERE id=$id";
        $result = $connection->query($sql); //zapytanie select zwróci obiekt
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedUser = new User();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashedPassword = $row['hashed_password'];
            $loadedUser->email = $row['email'];

            return $loadedUser;
        }
        return null;
    }

    static public function loadAllUsers(mysqli $connection) {
        $sql = "SELECT * FROM User";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedUser = new User();
                $loadedUser->id = $row['id'];
                $loadedUser->username = $row['username'];
                $loadedUser->email = $row['email'];
                $loadedUser->hashedPassword = $row['hashed_password'];

                $ret[] = $loadedUser;
            }
        }
        return $ret;
    }

    public function delete(mysqli $connection) {
        if ($this->id != -1) {
            $sql = "DELETE FROM User WHERE id=$this->id";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = -1;
                return true;
            }
            return false;
        }
        return true;
    }

    static public function loadUserByEmail(mysqli $connection, $email) {
        $sql = "SELECT * FROM User WHERE email='$email'";
        $result = $connection->query($sql); //zapytanie select zwróci obiekt
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedUser = new User();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashedPassword = $row['hashed_password'];
            $loadedUser->email = $row['email'];

            return $loadedUser;
        }
        return null;
    }

}
