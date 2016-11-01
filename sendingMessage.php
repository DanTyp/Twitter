<?php

require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Message.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['userID'])) {
        $_SESSION['receiverID'] = $_GET['userID'];

    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $messageText = $_POST['message'];
        
        $newMessage = new Message();
        $newMessage->setIdSender($_SESSION['loggedUserId']);
        $newMessage->setIdReceiver($_SESSION['receiverID']);
        $newMessage->setContent($messageText);
        $newMessage->setCreationDate();
        $newMessage->saveToDB($connection);
       
        $_SESSION['message_sent'] = 'Wiadomość została wysłana!';
        header('Location: sendingMessage.php'); //dzięki temu po odświeżeniu post nie doda się ponownie
        exit();
    } 
}



?>






<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Wysyłanie wiadomości</title>
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
            .c_info
            {
                color:green;
                margin-top: 10px;
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>
        <div id="Container">
            <?php
            echo '<div id="Container">';
            echo "<p><h3>Wysyłanie wiadomości</h3></p> ";
            
            
            
            ?>
            <form action="sendingMessage.php" method="POST">
                    <br>
                    <?php
                    
                    if (isset($_SESSION['message_sent'])) {
                        echo '<div class="c_info">' . $_SESSION['message_sent'] . '</div>';
                        unset($_SESSION['message_sent']);
                    }
                    ?>
                    Wpisz treść wiadomości:<br>
                    <textarea name="message" style="min-height: 150px; min-width: 400px;"></textarea>
                    <br>
                    <input type="submit" value="Wyślij" />
                    <br>
                </form>
            <a href="userPage.php">Powrót do strony użytkownika</a>
        </div>


    </body>
</html>