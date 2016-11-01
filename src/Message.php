<?php

require_once 'connection.php';

class Message {

    private $Id;
    private $Id_sender;
    private $Id_receiver;
    private $Content;
    private $Creation_date;
    private $Status;

    public function __construct() {
        $this->Id = -1;
        $this->Id_sender = 0;
        $this->Id_receiver = 0;
        $this->Content = '';
        $this->Creation_date = '';
        $this->Status = 0;
    }

    public function getId() {
        return $this->Id;
    }

    public function setIdSender($id) {
        $this->Id_sender = $id;
    }

    public function getIdSender() {
        return $this->Id_sender;
    }

    public function setIdReceiver($id) {
        $this->Id_receiver = $id;
    }

    public function getIdReceiver() {
        return $this->Id_receiver;
    }

    public function setContent($content) {
        $this->Content = $content;
    }

    public function getContent() {
        return $this->Content;
    }

    public function setCreationDate() {
        $this->Creation_date = date('Y-m-d H:i:s');
    }

    public function getCreationDate() {
        return $this->Creation_date;
    }

    public function setStatus($status) {
        $this->Status = $status;
    }

    public function getStatus() {
        return $this->Status;
    }

    //zapisywanie wiadomości do bazy lub aktualizacja statusu na przeczytany = 1
    public function saveToDB(mysqli $connection) {
        if ($this->Id == -1) {
            $sql = "INSERT INTO Message(Id_sender, Id_receiver, Content, Creation_date, Status)"
                    . "Values ('$this->Id_sender', '$this->Id_receiver', '$this->Content', '$this->Creation_date', '$this->Status')";

            $result = $connection->query($sql);
            if ($result == true) {
                $this->Id = $connection->insert_id;
                return true;
            }
        } else { //jeżeli taki wpis już istnieje to mogę go edytować, wówczas chcę żeby zmieniła się treść i data dodania
            $sql = "UPDATE Message SET Status = $this->Status WHERE Id = $this->Id";


            $result = $connection->query($sql);
            if ($result == true) {
                return true;
            }
        }
        return false;
    }

    //funkcja zwracająca wszystkie wysłane wiadomości według daty wysłania (najnowsze na górze)
    static public function loadAllMessagesBySenderIdAndByDate(mysqli $connection, $Id_sender) {
        $sql = "SELECT * FROM Message WHERE Id_sender=$Id_sender ORDER BY Creation_date DESC";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedMessage = new Message();
                $loadedMessage->Id = $row['Id'];
                $loadedMessage->Id_sender = $row['Id_sender'];
                $loadedMessage->Id_receiver = $row['Id_receiver'];
                $loadedMessage->Content = $row['Content'];
                $loadedMessage->Creation_date = $row['Creation_date'];
                $loadedMessage->Status = $row['Status'];


                $ret[] = $loadedMessage;
            }
        }
        return $ret;
    }

    static public function loadMessageById(mysqli $connection, $id) {
        $sql = "SELECT * FROM Message WHERE id=$id";
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedMessage = new Message();
            $loadedMessage->Id = $row['Id'];
            $loadedMessage->Id_sender = $row['Id_sender'];
            $loadedMessage->Id_receiver = $row['Id_receiver'];
            $loadedMessage->Content = $row['Content'];
            $loadedMessage->Creation_date = $row['Creation_date'];
            $loadedMessage->Status = $row['Status'];

            return $loadedMessage;
        }
        return null;
    }

    static public function loadAllMessagesByReceiverIdAndByDate(mysqli $connection, $Id_receiver) {
        $sql = "SELECT * FROM Message WHERE Id_receiver=$Id_receiver ORDER BY Creation_date DESC";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedMessage = new Message();
                $loadedMessage->Id = $row['Id'];
                $loadedMessage->Id_sender = $row['Id_sender'];
                $loadedMessage->Id_receiver = $row['Id_receiver'];
                $loadedMessage->Content = $row['Content'];
                $loadedMessage->Creation_date = $row['Creation_date'];
                $loadedMessage->Status = $row['Status'];


                $ret[] = $loadedMessage;
            }
        }
        return $ret;
    }

}
