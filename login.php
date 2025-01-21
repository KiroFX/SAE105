<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM joueur WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Motus - Connexion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1>Connexion</h1>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
    <form method="post">
        <div>
            <label>Login: <input type="text" name="login" required></label>
        </div>
        <div>
            <label>Mot de passe: <input type="password" name="password" required></label>
        </div>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>