<?php

require_once 'connection.php';

class Comment {

    private $Id;
    private $Id_usera;
    private $Id_postu;
    private $Creation_date;
    private $Text;

    public function __construct() {
        $this->Id = -1;
        $this->Id_usera = '';
        $this->Id_postu = '';
        $this->Creation_date;
        $this->Text = '';
    }

    public function getId() {
        return $this->Id;
    }

    public function setIdUsera($id) {
        $this->Id_usera = $id;
    }

    public function getIdUsera() {
        return $this->Id_usera;
    }

    public function setIdPostu($id) {
        $this->Id_postu = $id;
    }

    public function getIdPostu() {
        return $this->Id_postu;
    }

    public function setCreationDate() {
        $this->Creation_date = date('Y-m-d H:i:s');
    }

    public function getCreationDate() {
        return $this->Creation_date;
    }

    public function setText($text) {
        $this->Text = $text;
    }

    public function getText() {
        return $this->Text;
    }

    //FUNKCJA ZAPISYWANIA KOMENTARZA DO BAZY

    public function saveToDB(mysqli $connection) {
        if ($this->Id == -1) {
            $sql = "INSERT INTO Comment(Id_usera, Id_postu, Creation_date, Text)"
                    . "Values ('$this->Id_usera', '$this->Id_postu', '$this->Creation_date', '$this->Text')";

            $result = $connection->query($sql);
            if ($result == true) {
                $this->Id = $connection->insert_id;
                return true;
            }
        }
        return false;
    }

    //loadCommentById i loadAllCommentsByPostId - i po dacie od razu
    static public function loadCommentById(mysqli $connection, $id) {
        $sql = "SELECT * FROM Comment WHERE Id=$id";
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedComment = new Comment();
            $loadedComment->Id = $row['Id'];
            $loadedComment->Id_usera = $row['Id_usera'];
            $loadedComment->Id_postu = $row['Id_postu'];
            $loadedComment->Creation_date = $row['Creation_date'];
            $loadedComment->Text = $row['Text'];

            return $loadedComment;
        }
        return null;
    }

    static public function loadAllCommentsByPostIdAndByDate(mysqli $connection, $Id_postu) {
        $sql = "SELECT * FROM Comment WHERE Id_postu=$Id_postu ORDER BY Creation_date DESC";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedComment = new Comment();
                $loadedComment->Id = $row['Id'];
                $loadedComment->Id_usera = $row['Id_usera'];
                $loadedComment->Id_postu = $row['Id_postu'];
                $loadedComment->Creation_date = $row['Creation_date'];
                $loadedComment->Text = $row['Text'];

                $ret[] = $loadedComment;
            }
        }
        return $ret;
    }

}
