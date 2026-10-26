<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM equipos WHERE id=?'); $stmt->execute([(int)$_POST['id']]);
        flash('Equipo eliminado.'); redirect('equipos.php');
    }
    $nombre = trim($_POST['nombre'] ?? ''); $ciudad = trim($_POST['ciudad'] ?? '');
    $entrenador = trim($_POST['entrenador'] ?? '');
    if ($nombre !== '' && $ciudad !== '') {
        try {
            if ($action === 'edit') {
                $stmt=$pdo->prepare('UPDATE equipos SET nombre=?, ciudad=?, entrenador=? WHERE id=?');
                $stmt->execute([$nombre,$ciudad,$entrenador ?: null,(int)$_POST['id']]); flash('Equipo actualizado.');
            } else {
                $stmt=$pdo->prepare('INSERT INTO equipos(nombre,ciudad,entrenador) VALUES(?,?,?)');
                $stmt->execute([$nombre,$ciudad,$entrenador ?: null]); flash('Equipo registrado.');
            }
        } catch (PDOException $e) { flash('No se pudo guardar. Verifica que el nombre no esté repetido.'); }
    } else flash('Nombre y ciudad son obligatorios.');
    redirect('equipos.php');
}
$edit = null;
if (isset($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM equipos WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows=$pdo->query('SELECT e.*, (SELECT COUNT(*) FROM jugadores j WHERE j.equipo_id=e.id) AS total_jugadores FROM equipos e ORDER BY nombre')->fetchAll();
header_html('Gestión de equipos');
?>
<form method="post" class="form"><h3><?= $edit ? 'Editar equipo' : 'Registrar equipo' ?></h3><input type="hidden" name="action" value="<?= $edit?'edit':'create' ?>"><?php if($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?><label>Nombre del equipo<input name="nombre" required value="<?= e($edit['nombre'] ?? '') ?>"></label><label>Ciudad<input name="ciudad" required value="<?= e($edit['ciudad'] ?? '') ?>"></label><label>Entrenador<input name="entrenador" value="<?= e($edit['entrenador'] ?? '') ?>"></label><button>Guardar</button><?php if($edit): ?> <a href="equipos.php">Cancelar</a><?php endif; ?></form>
<div class="table-wrap"><table><tr><th>Equipo</th><th>Ciudad</th><th>Entrenador</th><th>Jugadores</th><th>Acciones</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['nombre']) ?></td><td><?= e($r['ciudad']) ?></td><td><?= e($r['entrenador']) ?></td><td><?= (int)$r['total_jugadores'] ?></td><td><a href="?edit=<?= (int)$r['id'] ?>">Editar</a><form class="inline" method="post" onsubmit="return confirm('¿Eliminar este equipo y sus datos relacionados?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach; ?>
</table></div>
<?php footer_html(); ?>
