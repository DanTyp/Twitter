<?php
require_once 'src/session.php';
require_once 'src/connection.php';
require_once 'src/User.php';

if (!isset($_SESSION['LoggedIn'])) {
    header('Location: index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userID = $_SESSION['loggedUserId'];

    $updatedUserById = User::loadUserById($connection, $userID);
    /* to chyba do usunięcia
      $newUserName = $loadedUserById->getName();
      $newEmail = $loadedUserById->getEmail();
      $newPassword = $loadedUserById->getHashedPassword();
     * 
     */

    switch ($_POST['submit']) {
        case 'username':
            $wszystko_OK = true;

            if (isset($_POST['username']) && is_string($_POST['username']) && strlen(trim($_POST['username'])) >= 3 &&
                    strlen(trim($_POST['username'])) <= 20 && ctype_alnum($_POST['username']) === true) {

                $newUserName = trim($_POST['username']);
                $updatedUserById->setUsername($newUserName);
            } else if (ctype_alnum($_POST['username']) == false) {

                $_SESSION['e_username'] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
                $wszystko_OK = false;
            } else {
                $_SESSION['e_username'] = "Nazwa użytkownika musi posiadać od 3 do 20 znaków!";
                $wszystko_OK = false;
            }

            $sql1 = "SELECT*FROM User WHERE username = '{$_POST['username']}'";
            $result1 = $connection->query($sql1);
            $identyczna_nazwa = $result1->num_rows;
            if ($identyczna_nazwa > 0) {
                $wszystko_OK = false;
                $_SESSION['e_username'] = "Istnieje już użytkownik o podanej nazwie!";
            }

            if ($wszystko_OK == true) {
                $updatedUserById->saveToDB($connection);
                $_SESSION['new_username'] = 'Nazwa użytkownika została zmieniona!';
            }


            break;



        case 'email':
            $wszystko_OK = true;
            if (isset($_POST['email']) && is_string($_POST['email'])) {
                $email = $_POST['email'];
                $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

                if (filter_var($emailB, FILTER_VALIDATE_EMAIL) == true && ($emailB == $email)) {
                    $updatedUserById->setEmail($email);
                } else {
                    $_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
                    $wszystko_OK = false;
                }
            }

            $sql2 = "SELECT*FROM User WHERE email = '$email'";
            $result2 = $connection->query($sql2);
            $identyczny_email = $result2->num_rows;
            if ($identyczny_email > 0) {
                $wszystko_OK = false;
                $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
            }

            if ($wszystko_OK == true) {
                $updatedUserById->saveToDB($connection);
                $_SESSION['new_email'] = 'Adres e-mail został zmieniony!';
            }
            break;




        case 'password':
            $wszystko_OK = true;
            if (isset($_POST['password1']) && isset($_POST['password2'])) {
                $password1 = $_POST['password1'];
                $password2 = $_POST['password2'];

                if (strlen($password1) >= 8 && strlen($password1) <= 20 && $password1 === $password2) {
                    $updatedUserById->setHashedPassword($password1);
                } else if (strlen($password1) < 8 || strlen($password1) > 20) {
                    $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
                    $wszystko_OK = false;
                } else {
                    $_SESSION['e_password'] = "Podane hasła nie są identyczne!";
                    $wszystko_OK = false;
                }
            }

            if ($wszystko_OK == true) {
                $updatedUserById->saveToDB($connection);
                $_SESSION['new_password'] = 'Hasło zostało zmienione!';
            }
            break;
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Twitter - edycja danych</title>
        <style>
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
        <div>
            <form method="POST" action="editUser.php">
                Nowa nazwa użytkownika: <br> <input type="text" name="username"><br>
                <button type="submit" name="submit" value="username">Zapisz zmiany</button>
                <?php
                if (isset($_SESSION['e_username'])) {
                    echo '<div class="error">' . $_SESSION['e_username'] . '</div>';
                    unset($_SESSION['e_username']);
                }
                if (isset($_SESSION['new_username'])) {
                    echo '<div class="info">' . $_SESSION['new_username'] . '</div>';
                    unset($_SESSION['new_username']);
                }
                
                ?>
            </form>
        </div>


        <div>
            <form method="POST" action="editUser.php">
                Nowy e-mail: <br> <input type="text" name="email"><br>
                <button type="submit" name="submit" value="email">Zapisz zmiany</button>
                <?php
                if (isset($_SESSION['e_email'])) {
                    echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                    unset($_SESSION['e_email']);
                }
                if (isset($_SESSION['new_email'])) {
                    echo '<div class="info">' . $_SESSION['new_email'] . '</div>';
                    unset($_SESSION['new_email']);
                }
                ?>
            </form>
        </div>


        <div>
            <form method="POST" action="editUser.php">

                Podaj nowe hasło: <br> <input type="password" name="password1"><br>
                Powtórz nowe hasło: <br> <input type="password" name="password2"><br>
                <button type="submit" name="submit" value="password">Zapisz zmiany</button>

                <?php
                if (isset($_SESSION['e_password'])) {
                    echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                    unset($_SESSION['e_password']);
                }
                if (isset($_SESSION['new_password'])) {
                    echo '<div class="info">' . $_SESSION['new_password'] . '</div>';
                    unset($_SESSION['new_password']);
                }
                ?>

                <br><br>
                <a href="mainpage.php">Powrót do strony głównej</a>
            </form>
        </div>

    </body>
</html>