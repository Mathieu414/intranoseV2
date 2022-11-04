<?php

require_once "core/db.php";
require_once "core/utils.php";

if (count($_POST) && (!empty($_POST['login']) || !empty($_POST['password']))) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $user_data = fetch(
        "SELECT id, perm
        FROM licencies
        WHERE login=? AND password=MD5(?) LIMIT 1;",
        [$login, $password]
    );
    if (count($user_data)) {
        $_SESSION['user_id'] = $user_data['id'];
        redirect("accueil.php");
    }
}

$title = "Login";
$css = "assets/css/login.css";
ob_start();
?>

<main class="container small">
    <article class="grid">
        <div>
            <h1>Intranose</h1>
            <form method="post">
                <input type="text" name="login" placeholder="Login" aria-label="Login" autocomplete="off" required>
                <input type="password" name="password" placeholder="Mot de passe" aria-label="Mot de passe" autocomplete="current-password" required>
                <fieldset>
                    <label for="remember">
                        <input type="checkbox" role="switch" id="remember" name="remember">
                        Se souvenir de moi
                    </label>
                </fieldset>
                <button type="submit">Se connecter</button>
            </form>
        </div>
    </article>
</main>

<?php
$content = ob_get_clean();
require "template/layout.php";
