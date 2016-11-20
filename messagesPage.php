<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Message.php';
require_once 'src/User.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}
?>


<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Skrzynka pocztowa</title>
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
            echo "<p><h3>Skrzynka pocztowa</h3></p> ";
            echo "<p><h4>Wiadomości odebrane:</h4></p>";

            $allReceivedMessages = Message::loadAllMessagesByReceiverIdAndByDate($connection, $_SESSION['loggedUserId']);

            foreach ($allReceivedMessages as $message) {

                $status = $message->getStatus();
                $messageId = $message->getId();
                $senderID = $message->getIdSender();
                $sender = User::loadUserById($connection, $senderID);
                $senderName = $sender->getName();
                $messageContent = $message->getContent();
                $cutMessage = substr($messageContent, 0, 30);

                if ($status == 0) { //jeżeli wiadomość jest nie przeczytana
                    $_SESSION['zeroStatus'] = true; //jeżeli wiadomość nie została przeczytana
                    echo "<b><a style='color: #B8860B;' href='messageContent.php?messageId=$messageId'>$cutMessage</a></b>"; 
                    ////nieprzeczytane wiadomości
                //są pogrubione
                } else {
                    echo "<a href='messageContent.php?messageId=$messageId'>$cutMessage</a>";
                }

                
                echo '<br>';
                echo $message->getContent();
                echo '<br>';
                echo "Nadawca: " . $senderName;
                echo '<br>';
                echo "Data wysłania: " . $message->getCreationDate();
                echo '<br><hr/>';
            }


            
            echo "<p><h4>Wiadomości wysłane:</h4></p>";

            $allSentMessages = Message::loadAllMessagesBySenderIdAndByDate($connection, $_SESSION['loggedUserId']);

            if ($allSentMessages != []) {
                foreach ($allSentMessages as $message) {
                    $messageId = $message->getId();
                    $receiverID = $message->getIdReceiver();
                    $receiver = User::loadUserById($connection, $receiverID);
                    $receiverName = $receiver->getName();
                    //$sender = User::loadUserById($connection, $_SESSION['loggedUserId']);
                    //$_SESSION['senderName'] = $sender->getName();
                    $messageContent = $message->getContent();
                    $cutMessage = substr($messageContent, 0, 30);
                    echo "<a href='messageContent.php?messageId=$messageId'>$cutMessage</a>";
                    echo '<br>';
                    //echo $message->getContent();
                    //echo '<br>';
                    echo "Wysłano do: " . $receiverName;
                    echo '<br>';
                    echo "Data wysłania: " . $message->getCreationDate();
                    //echo '<br>';
                    //echo "Wysłane przez ".$_SESSION['senderName']; //sprawdzenie poprawnosci
                    echo '<br><hr/>';
                }
            }
            echo '<a href="mainpage.php">Powrót do strony głównej</a>';
            echo '</div>';
            ?>





        </div>


    </body>
</html>