<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Message.php';
require_once 'src/User.php';

//var_dump($_SESSION['zeroStatus']); //sprawdzenie

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['messageId'])) {
        $messageID = $_GET['messageId'];
    }
}
?>


<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Strona wiadomosci</title>
        <style>
            #Container
            {
                width:700px;
                background-color: lightblue;
                margin-right:auto;
                margin-left:auto;
                margin-top:10px;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div id="Container">
            <?php
            echo '<div id="Container">';
            echo "<p><h3>Strona wiadomości</h3></p> ";



            $message = Message::loadMessageById($connection, $messageID);
            $senderId = $message->getIdSender();
            $receiverId = $message->getIdReceiver();
            $content = $message->getContent();
            $creationDate = $message->getCreationDate();

            $sender = User::loadUserById($connection, $senderId);
            $senderName = $sender->getName();

            $receiver = User::loadUserById($connection, $receiverId);
            $receiverName = $receiver->getName();

            echo $content;
            echo '<br>';
            echo 'Nadawca: ' . $senderName;
            echo '<br>';
            echo 'Odbiorca: ' . $receiverName;
            echo '<br>';
            echo 'Data wysłania: ' . $creationDate;
            echo '<br><hr/>';

            echo '<a href="messagesPage.php">Powrót do skrzynki wiadomości</a>';

            //to tylko dla nieprzeczytanych, bo nie chce np ustawić statusu wiadomości wysłanej przeze mnie, a nie przeczytanej na 1
            
            if (isset($_SESSION['zeroStatus']) && $_SESSION['zeroStatus'] === true) {
                $message->setStatus(1);
                $message->saveToDB($connection);
                unset($_SESSION['zeroStatus']);
            }


            echo '</div>';
            ?>






        </div>


    </body>
</html>