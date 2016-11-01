<?php
require_once 'src/session.php';
require_once 'src/User.php'; // w klasię User trzeba dodać metodę loadUserByEmail
require_once 'src/connection.php';


if (isset($_SESSION['LoggedIn'])) {
    header('Location: mainpage.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && $_POST['password']) {

        $email = $_POST['email'];
        $password = $_POST['password'];
        $loggedUser = User::loadUserByEmail($connection, $email);

        if ($loggedUser != null) { //jeżeli znaleźliśmy takiego usera
            $hashedPassword = $loggedUser->getHashedPassword();
            if (password_verify($password, $hashedPassword)) {
                $loggedUserId = $loggedUser->getID();
                $_SESSION['loggedUserId'] = $loggedUserId; //będę wrzucał ID zalogowanego Usera do sesji, żeby mieć do niego 
                //dostęp na innych stronach
                $loggedUserName = $loggedUser->getName();
                $_SESSION['loggedUserName'] = $loggedUserName;
                $_SESSION['LoggedIn'] = true;
                header('Location: mainpage.php');
            } else {
                $_SESSION['e_email'] = "Niepoprawny e-mail lub hasło. Spróbuj ponownie";
            }
        } else {
            $_SESSION['e_email'] = "Niepoprawny e-mail lub hasło. Spróbuj ponownie";
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Twitter - zaloguj się!</title>
        <style>
            .error
            {
                color:red;
                margin-top: 10px;
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>

        Witaj na Twitterze! Zaloguj się lub zarejestruj!<br /><br />

        <!--poniżej tworzę linka, który umożliwi nam przejście do formularza rejestracji-->
        <a href="register.php">Rejestracja - załóż darmowe konto!</a>
        <br><br>

        <form action="index.php" method="POST">

            E-mail:<br> <input type = "text" name="email" /> <br>
            Hasło:<br> <input type = "password" name="password" /> <br><br>
            <input type="submit" value="Zaloguj się" />
            <?php
            if (isset($_SESSION['e_email'])) {
                echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                unset($_SESSION['e_email']);
            }
            ?>

        </form>



    </body>
</html>