<?php
require_once 'src/session.php';
require_once 'src/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wszystko_OK = true;
    $newUser = new User();

    //waliduję nazwę użytkownika

    if (isset($_POST['username']) && is_string($_POST['username']) && strlen(trim($_POST['username'])) >= 3 &&
            strlen(trim($_POST['username'])) <= 20 && ctype_alnum($_POST['username']) === true) {

        $userName = trim($_POST['username']);
        $newUser->setUsername($userName);
    } else if (ctype_alnum($_POST['username']) == false) {

        $_SESSION['e_username'] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
        $wszystko_OK = false;
    } else {
        $_SESSION['e_username'] = "Nazwa użytkownika musi posiadać od 3 do 20 znaków!";
        $wszystko_OK = false;
    }

    //waliduję email

    if (isset($_POST['email']) && is_string($_POST['email'])) {
        $email = $_POST['email'];
        $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (filter_var($emailB, FILTER_VALIDATE_EMAIL) == true && ($emailB == $email)) {
            $newUser->setEmail($email);
        } else {
            $_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
            $wszystko_OK = false;
        }
    }

    //waliduję hasła

    if (isset($_POST['password1']) && isset($_POST['password2'])) {
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];

        if (strlen($password1) >= 8 && strlen($password1) <= 20 && $password1 === $password2) {
            $newUser->setHashedPassword($password1);
        } else if (strlen($password1) < 8 || strlen($password1) > 20) {
            $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
            $wszystko_OK = false;
        } else {
            $_SESSION['e_password'] = "Podane hasła nie są identyczne!";
            $wszystko_OK = false;
        }
    }
    /* poniższy komunikat nigdy się nie wyświetlał, zawsze najpierw wychwytywało fakt że hasła nie są indentyczne
      else if (!isset($_POST['password1']) || !isset($_POST['password2'])) {
      $_SESSION['e_password'] = "Powtórz hasło!";
      }
     * 
     */

    //sprawdzam akceptację regulaminu

    if (!isset($_POST['regulamin'])) {
        $wszystko_OK = false;
        $_SESSION['e_regulamin'] = "Potwierdź akceptację regulaminu!";
    }
    
    require_once 'src/connection.php';
        
        //sprawdzam czy podana nazwa użytkownika nie jest już zajęta
        //$sql1 = "SELECT*FROM User WHERE username = '$userName'"; <- to nie działa - dlaczego
        $sql1 = "SELECT*FROM User WHERE username = '{$_POST['username']}'";
        $result1 = $connection->query($sql1);
        $identyczna_nazwa = $result1->num_rows;
        if($identyczna_nazwa > 0){
            $wszystko_OK = false;
            $_SESSION['e_username'] = "Istnieje już użytkownik o podanej nazwie!";
            
        }
        
        //sprawdzam czy podany email nie jest już zajęty
        $sql2 = "SELECT*FROM User WHERE email = '$email'";
        $result2 = $connection->query($sql2);
        $identyczny_email = $result2->num_rows;
        if($identyczny_email > 0){
            $wszystko_OK = false;
            $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";	
        }
        /* ta walidacja pokazała, że wyszukanie identycznego emaila dziąła, a dla nazwy użytkownika nie...
        $row = $result1->fetch_assoc();
        $id=$row['id'];
        var_dump($id);
         * 
         */
        
    //JEŻELI WALIDACJA SIĘ UDAŁA -> WSTAWIAM USERA DO TABLICY USER!!!!
    if ($wszystko_OK == true) { 

        $newUser->saveToDB($connection);
        $_SESSION['LoggedIn'] = true;
        $loggedUserId = $newUser->getID();
        $_SESSION['loggedUserId'] = $loggedUserId;
        $loggedUserName = $newUser->getName();
        $_SESSION['loggedUserName'] = $loggedUserName;
        header('Location: mainpage.php');
        
    }
    
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>Twitter - załóż darmowe konto!</title>
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
        <form method="POST">

            Nazwa użytkownika: <br> <input type="text" name="username"><br>

            <?php
            if (isset($_SESSION['e_username'])) {
                echo '<div class="error">' . $_SESSION['e_username'] . '</div>';
                unset($_SESSION['e_username']);
            }
            ?>

            E-mail: <br> <input type="text" name="email"><br>

            <?php
            if (isset($_SESSION['e_email'])) {
                echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                unset($_SESSION['e_email']);
            }
            ?>

            Twoje hasło: <br> <input type="password" name="password1"><br>


            Powtórz hasło: <br> <input type="password" name="password2"><br>

            <?php
            if (isset($_SESSION['e_password'])) {
                echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                unset($_SESSION['e_password']);
            }
            ?>

            <label>
                <input type="checkbox" name="regulamin" />Akceptuję regulamin
            </label>
            <?php
            if (isset($_SESSION['e_regulamin'])) {
                echo '<div class="error">' . $_SESSION['e_regulamin'] . '</div>';
                unset($_SESSION['e_regulamin']);
            }
            ?>

            <br>

            <input type="submit" value="Zarejestruj się" />
            <br>
            <a href="index.php">Powrót do strony logowania</a>

        </form>
    </body>
</html>

