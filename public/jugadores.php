<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $s=$pdo->prepare('DELETE FROM jugadores WHERE id=?'); $s->execute([(int)$_POST['id']]); flash('Jugador eliminado.'); redirect('jugadores.php');
    }
    $data=[(int)($_POST['equipo_id']??0),trim($_POST['nombres']??''),trim($_POST['apellidos']??''),(int)($_POST['dorsal']??0),$_POST['posicion']??'Base',(int)($_POST['edad']??0)];
    if ($data[0]>0 && $data[1]!=='' && $data[2]!=='' && $data[3]>=0 && $data[3]<=99 && $data[5]>=10 && $data[5]<=80) {
        try {
            if (($_POST['action']??'')==='edit') { $data[]=(int)$_POST['id']; $s=$pdo->prepare('UPDATE jugadores SET equipo_id=?,nombres=?,apellidos=?,dorsal=?,posicion=?,edad=? WHERE id=?'); $s->execute($data); flash('Jugador actualizado.'); }
            else { $s=$pdo->prepare('INSERT INTO jugadores(equipo_id,nombres,apellidos,dorsal,posicion,edad) VALUES(?,?,?,?,?,?)'); $s->execute($data); flash('Jugador registrado.'); }
        } catch(PDOException $e) { flash('No se pudo guardar. Revisa el dorsal y los datos.'); }
    } else flash('Completa los campos correctamente.');
    redirect('jugadores.php');
}
$edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM jugadores WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$teams=$pdo->query('SELECT id,nombre FROM equipos ORDER BY nombre')->fetchAll();
$rows=$pdo->query('SELECT j.*,e.nombre AS equipo FROM jugadores j JOIN equipos e ON e.id=j.equipo_id ORDER BY e.nombre,j.apellidos')->fetchAll();
header_html('Gestión de jugadores');
?>
<?php if(!$teams): ?><p>Primero debes <a href="equipos.php">registrar un equipo</a>.</p><?php else: ?>
<form method="post" class="form"><h3><?= $edit?'Editar jugador':'Registrar jugador' ?></h3><input type="hidden" name="action" value="<?= $edit?'edit':'create' ?>"><?php if($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?><label>Equipo<select name="equipo_id" required><?php foreach($teams as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)($edit['equipo_id']??0)===(int)$t['id']?'selected':'' ?>><?= e($t['nombre']) ?></option><?php endforeach; ?></select></label><label>Nombres<input name="nombres" required value="<?= e($edit['nombres']??'') ?>"></label><label>Apellidos<input name="apellidos" required value="<?= e($edit['apellidos']??'') ?>"></label><label>Dorsal (0–99)<input type="number" name="dorsal" min="0" max="99" required value="<?= e((string)($edit['dorsal']??'')) ?>"></label><label>Posición<select name="posicion"><?php foreach(['Base','Escolta','Alero','Ala-pívot','Pívot'] as $p): ?><option <?= ($edit['posicion']??'')===$p?'selected':'' ?>><?= e($p) ?></option><?php endforeach; ?></select></label><label>Edad<input type="number" name="edad" min="10" max="80" required value="<?= e((string)($edit['edad']??'')) ?>"></label><button>Guardar jugador</button><?php if($edit): ?> <a href="jugadores.php">Cancelar</a><?php endif; ?></form><?php endif; ?>
<div class="table-wrap"><table><tr><th>Jugador</th><th>Equipo</th><th>Dorsal</th><th>Posición</th><th>Edad</th><th>Acciones</th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['nombres'].' '.$r['apellidos']) ?></td><td><?= e($r['equipo']) ?></td><td><?= (int)$r['dorsal'] ?></td><td><?= e($r['posicion']) ?></td><td><?= (int)$r['edad'] ?></td><td><a href="?edit=<?= (int)$r['id'] ?>">Editar</a> <form class="inline" method="post" onsubmit="return confirm('¿Eliminar jugador?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach; ?></table></div>
<?php footer_html(); ?>
