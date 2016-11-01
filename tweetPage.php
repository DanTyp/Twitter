<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/Tweet.php';
require_once 'src/User.php';
require_once 'src/Comment.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['tweet_ID'])) {
        $tweetID = $_GET['tweet_ID']; //numer tweeta/postu który przyszedł GETem
        $_SESSION['IDpostu'] = $tweetID; //wrzucam to do sesji, ponieważ znika, kiedy przesyłam POStem nowy komentarz
        $tweetById = Tweet::loadTweetById($connection, $tweetID);
        $authorsId = $tweetById->getUserId(); //wyciągam id autora
        $author = User::loadUserById($connection, $authorsId);
        $authorsName = $author->getName(); //wyciągam nazwe autora
        $_SESSION['authorsName'] = $authorsName; //wrzucam to do sesji, ponieważ znika, kiedy przesyłam POStem nowy komentarz
        echo $authorsName;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment']) && is_string($_POST['comment']) && strlen($_POST['comment']) > 0 && strlen($_POST['comment']) <= 160) {
        $CommentText = $_POST['comment'];
        $newComment = new Comment();
        $newComment->setIdUsera($_SESSION['loggedUserId']);
        $newComment->setIdPostu($_SESSION['IDpostu']);
        $newComment->setCreationDate();
        $newComment->setText($CommentText);
        $newComment->saveToDB($connection);
        $_SESSION['correct_comment'] = 'Komentarz został dodany';
        header('Location: tweetPage.php'); //dzięki temu po odświeżeniu post nie doda się ponownie
        exit();
    } else {
        $_SESSION['wrong_comment'] = 'Komentarze mogą zawierać od 1 do 60 znaków!';
    }
}
?>


<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Twitter - strona wpisu!</title>
        <style>
            #Container
            {
                width:700px;
                background-color: lightblue;
                margin-right:auto;
                margin-left:auto;
                margin-top:10px;
            }
            #NoComments
            {
                color: red;
            }
            .c_error
            {
                color:red;
                margin-top: 10px;
                margin-bottom: 10px;
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
            echo 'Witaj na stronie wpisu nr ' . $_SESSION['IDpostu'] . '<b>' . '</b>' . ' , poniżej znajdziesz wszystkie informacje na jego temat!<br>';
            echo '<br><br>';
            //$tweet = Tweet::loadTweetById($connection, $tweetID);
            $loadedTweet1 = Tweet::loadTweetById($connection, $_SESSION['IDpostu']);

            //echo $loadedTweet1->getId();
            //echo '<br>';
            //echo $loadedTweet1->getUserId();
            //echo '<br>';
            echo '<b>' . $loadedTweet1->getText() . '</b>';
            echo '<br>';
            echo 'Użytkownik: ' . $_SESSION['authorsName'];
            echo '<br>';
            echo $loadedTweet1->getCreationDate();
            echo '<br>';
            echo '<br>';
            echo 'Komentarze:';
            //poniżej dodaję wyświetlanie wszystkich komentarzy do 

            $allCommentsToThisTweet = Comment::loadAllCommentsByPostIdAndByDate($connection, $_SESSION['IDpostu']);
            if ($allCommentsToThisTweet != []) { // if(!empty(allCommentsToThisTweet) -> jeżeli tablica nie jest pust
                ?>
                <ul>
                    <?php
                    foreach ($allCommentsToThisTweet as $comment) {

                        $commentAuthorId = $comment->getIdUsera();
                        $user = User::loadUserById($connection, $commentAuthorId);
                        $commentAuthorName = $user->getName();

                        echo '<li>';
                        echo $comment->getText();
                        echo '<br>';
                        //echo $comment->getId();
                        //echo '<br>';
                        echo "Dodane przez: $commentAuthorName";
                        echo '<br>';
                        //echo $comment->getIdUsera();
                        //echo '<br>';
                        //echo $comment->getIdPostu();
                        //echo '<br>';
                        echo $comment->getCreationDate();
                        echo '<br><hr/>';
                        echo '</li>';
                    }
                } else {
                    echo '<div id="NoComments">';
                    echo 'Brak komentarzy';
                    echo '</div>';
                }
                ?>
                <form action="tweetPage.php" method="POST">
                    <br>
                    <?php
                    if (isset($_SESSION['wrong_comment'])) {
                        echo '<div class="c_error">' . $_SESSION['wrong_comment'] . '</div>';
                        unset($_SESSION['wrong_comment']);
                    }
                    if (isset($_SESSION['correct_comment'])) {
                        echo '<div class="c_info">' . $_SESSION['correct_comment'] . '</div>';
                        unset($_SESSION['correct_comment']);
                    }
                    ?>
                    Dodaj komentarz:<br>
                    <textarea name="comment" style="min-height: 100px; min-width: 300px;"></textarea>
                    <br>
                    <input type="submit" value="Dodaj" />
                    <br>
                </form>
                <a href="mainpage.php">Powrót do strony głównej</a>
        </div>
    </body>
</html>