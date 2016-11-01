<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Tweet.php';
require_once 'src/User.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tweet']) && is_string($_POST['tweet']) && strlen($_POST['tweet']) > 0 && strlen($_POST['tweet']) <= 160) {
        $newPost = $_POST['tweet'];
        $newTweet = new Tweet();
        $newTweet->setUserId($_SESSION['loggedUserId']);
        $newTweet->setText($newPost);
        $newTweet->setCreationDate();
        $newTweet->saveToDB($connection);
        $_SESSION['correct_post'] = 'Post został dodany';
        header('Location: mainpage.php'); //dzięki temu po odświeżeniu post nie doda się ponownie
        exit();
    } else {
        $_SESSION['wrong_post'] = 'Posty mogą zawierać od 1 do 160 znaków!';
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Strona główna</title>
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
            .error
            {
                color:red;
                margin-top: 10px;
                margin-bottom: 10px;
            }
            .info
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
            echo "<p>Witaj na stronie głównej " . $_SESSION['loggedUserName'] . '!' .
            '&nbsp &nbsp' . '<a href="editUser.php">Edytuj swoje dane</a>' . '&nbsp &nbsp' .
                    '<a href="messagesPage.php">Wiadomości</a>'.'&nbsp &nbsp' .'<a href="logout.php">Wyloguj się!</a>';
                    
            echo '</div>';
            ?>
            <br>

            <div style=" width: 500px; margin-left: auto; margin-right: auto;">
                <form action="mainpage.php" method="POST">
                    <?php
                    if (isset($_SESSION['wrong_post'])) {
                        echo '<div class="error">' . $_SESSION['wrong_post'] . '</div>';
                        unset($_SESSION['wrong_post']);
                    }
                    if (isset($_SESSION['correct_post'])) {
                        echo '<div class="info">' . $_SESSION['correct_post'] . '</div>';
                        unset($_SESSION['correct_post']);
                    }
                    ?>
                    Dodaj post:<br>
                    <textarea name="tweet" style="min-height: 100px; min-width: 400px;"></textarea>
                    <br>
                    <input type="submit" value="Dodaj" />

                </form>
            </div>

            <?php
            //poniżej trzeba będzie zrobić drugą pętlę
            $allTweets = Tweet::loadAllTweetsByDate($connection); //trzeba posortować po dacie -> nowa metoda loadAllTweetsByDate
            foreach ($allTweets as $tweet) {
                $tweetId = $tweet->getId();
                //var_dump($tweetId);
                $userId = $tweet->getUserId();
                $user = User::loadUserById($connection, $userId);
                $userName = $user->getName();
                //var_dump($userId);
                //zmienne sesyjne są tu niepotrzebne, przesyłam dane GETem
                //$_SESSION['tweetId'] = $tweetId;
                //$_SESSION['userId'] = $userId;
                //$tweet_ID = $_SESSION['tweetId'];
                //echo '<label>';
                //echo '<a href="stronaWpisu.php">';
                echo $tweet->getText();
                echo '<br>';
                echo 'Użytkownik: ' . $userName;
                echo '<br>';
                echo $tweet->getCreationDate();
                echo '<br>';
                //echo 'ID wpisu: ' . $tweetId;
                //echo '<br>';
                //echo 'ID Użytkownika: ' . $userId;
                //echo '<br>';

                echo "<a href='tweetPage.php?tweet_ID=$tweetId'>Szczegóły wpisu</a>";
                echo '&nbsp &nbsp';
                echo "<a href='userPage.php?user_ID=$userId'>Strona Użytkownika</a>";
                echo '<br><hr/>';
                //echo '</a>';
                //echo '</label>';
                //echo $tweet->getUserId();
                //echo '<br><hr/>';
            }
            ?>

            
        </div>


    </body>
</html>