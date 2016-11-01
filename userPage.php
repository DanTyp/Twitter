<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Tweet.php';
require_once 'src/User.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_ID'])) {
        $authorId = $_GET['user_ID'];

        $user = User::loadUserById($connection, $authorId);
        $authorName = $user->getName();
    }
}
?>


<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Twitter - strona użytkownika!</title>
        <style>
            #Container
            {
                width:700px;
                background-color: lightblue;
                margin-right:auto;
                margin-left:auto;
                margin-top:10px;
            }
        </style>


    </head>

    <body>
        <div id="Container">
        <?php
        echo 'Witaj na stronie użytkownika ' . '<b>' . $authorName . '</b>' . ' , poniżej możesz przeczytać wszystkie jego wpisy!<br>';
        echo '<br><br>';
        //var_dump($_GET['user_ID']);
        $tweetsByIdAndByDate = Tweet::loadAllTweetsByUserIdAndByDate($connection, $authorId);

        foreach ($tweetsByIdAndByDate as $tweet) {
            echo $tweet->getText();
            echo '<br>';
            echo $tweet->getCreationDate();
            echo '<br><hr/>';
        }
        echo '<br>';
        ?>
        <a href="mainpage.php">Powrót do głównej</a>
        </div>
    </body>
</html>