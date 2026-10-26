<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if ($nombre === '' || !$email || strlen($password) < 8) {
        $message = 'Completa los campos. La contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nombre,email,password_hash) VALUES (?,?,?)');
            $stmt->execute([$nombre,$email,password_hash($password, PASSWORD_DEFAULT)]);
            $message = 'Usuario creado. Ya puedes iniciar sesión. Por seguridad, elimina este archivo.';
        } catch (PDOException $e) { $message = 'No se pudo crear el usuario. Puede que el correo ya exista.'; }
    }
}
header_html('Crear usuario');
?>
<?php if ($message): ?><p class="flash"><?= e($message) ?></p><?php endif; ?>
<form method="post" class="form"><label>Nombre<input name="nombre" required maxlength="100"></label><label>Correo electrónico<input type="email" name="email" required></label><label>Contraseña (mínimo 8 caracteres)<input type="password" name="password" minlength="8" required></label><button>Crear usuario</button></form>
<p><a href="login.php">Ir al inicio de sesión</a></p>
<?php footer_html(); ?>
