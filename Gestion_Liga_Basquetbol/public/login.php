<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
if (!empty($_SESSION['user_id'])) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT id,nombre,password_hash FROM usuarios WHERE email = ?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        redirect('index.php');
    }
    $error = 'Correo o contraseña incorrectos.';
}
header_html('Iniciar sesión');
?>
<?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="form"><label>Correo electrónico<input type="email" name="email" required></label><label>Contraseña<input type="password" name="password" required></label><button>Ingresar</button></form>
<p>Primera vez: <a href="crear_usuario.php">crear usuario</a>.</p>
<?php footer_html(); ?>
