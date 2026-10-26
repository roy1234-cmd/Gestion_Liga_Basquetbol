<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action']??'')==='delete') { $s=$pdo->prepare('DELETE FROM partidos WHERE id=?'); $s->execute([(int)$_POST['id']]); flash('Partido eliminado.'); redirect('partidos.php'); }
    $local=(int)($_POST['local_id']??0); $visitante=(int)($_POST['visitante_id']??0);
    $fecha=str_replace('T',' ',trim($_POST['fecha']??'')); $estado=$_POST['estado']??'Programado';
    $pl=$_POST['puntos_local']??''; $pv=$_POST['puntos_visitante']??'';
    if ($local>0 && $visitante>0 && $local!==$visitante && $fecha!=='' && in_array($estado,['Programado','Finalizado'],true) && ($estado!=='Finalizado'||($pl!==''&&$pv!==''&&is_numeric($pl)&&is_numeric($pv)&&$pl>=0&&$pv>=0))) {
        try {
            $pl=$estado==='Finalizado'?(int)$pl:null; $pv=$estado==='Finalizado'?(int)$pv:null;
            if (($_POST['action']??'')==='edit') { $s=$pdo->prepare('UPDATE partidos SET local_id=?,visitante_id=?,fecha=?,puntos_local=?,puntos_visitante=?,estado=? WHERE id=?'); $s->execute([$local,$visitante,$fecha,$pl,$pv,$estado,(int)$_POST['id']]); flash('Partido actualizado.'); }
            else { $s=$pdo->prepare('INSERT INTO partidos(local_id,visitante_id,fecha,puntos_local,puntos_visitante,estado) VALUES(?,?,?,?,?,?)'); $s->execute([$local,$visitante,$fecha,$pl,$pv,$estado]); flash('Partido registrado.'); }
        } catch(PDOException $e) { flash('No se pudo guardar el partido. Verifica los equipos y la fecha.'); }
    } else flash('Completa correctamente los campos; un equipo no puede jugar contra sí mismo.');
    redirect('partidos.php');
}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM partidos WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$teams=$pdo->query('SELECT id,nombre FROM equipos ORDER BY nombre')->fetchAll();
$rows=$pdo->query('SELECT p.*,l.nombre AS local,v.nombre AS visitante FROM partidos p JOIN equipos l ON l.id=p.local_id JOIN equipos v ON v.id=p.visitante_id ORDER BY p.fecha DESC')->fetchAll();
header_html('Gestión de partidos');
?>
<?php if(count($teams)<2): ?><p>Registra al menos dos <a href="equipos.php">equipos</a> para programar partidos.</p><?php else: ?>
<form method="post" class="form"><h3><?= $edit?'Editar partido':'Registrar partido' ?></h3><input type="hidden" name="action" value="<?= $edit?'edit':'create' ?>"><?php if($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?><label>Equipo local<select name="local_id"><?php foreach($teams as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)($edit['local_id']??0)===(int)$t['id']?'selected':'' ?>><?= e($t['nombre']) ?></option><?php endforeach; ?></select></label><label>Equipo visitante<select name="visitante_id"><?php foreach($teams as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)($edit['visitante_id']??0)===(int)$t['id']?'selected':'' ?>><?= e($t['nombre']) ?></option><?php endforeach; ?></select></label><label>Fecha y hora<input type="datetime-local" name="fecha" required value="<?= isset($edit['fecha'])?e(date('Y-m-d\TH:i',strtotime($edit['fecha']))):'' ?>"></label><label>Estado<select name="estado"><option <?= ($edit['estado']??'Programado')==='Programado'?'selected':'' ?>>Programado</option><option <?= ($edit['estado']??'')==='Finalizado'?'selected':'' ?>>Finalizado</option></select></label><label>Puntos local (si finalizó)<input type="number" min="0" name="puntos_local" value="<?= e((string)($edit['puntos_local']??'')) ?>"></label><label>Puntos visitante (si finalizó)<input type="number" min="0" name="puntos_visitante" value="<?= e((string)($edit['puntos_visitante']??'')) ?>"></label><button>Guardar partido</button><?php if($edit): ?> <a href="partidos.php">Cancelar</a><?php endif; ?></form><?php endif; ?>
<div class="table-wrap"><table><tr><th>Fecha</th><th>Encuentro</th><th>Marcador</th><th>Estado</th><th>Acciones</th></tr><?php foreach($rows as $r): ?><tr><td><?= e(date('d/m/Y H:i',strtotime($r['fecha']))) ?></td><td><?= e($r['local'].' vs '.$r['visitante']) ?></td><td><?= $r['estado']==='Finalizado'?(int)$r['puntos_local'].' - '.(int)$r['puntos_visitante']:'Pendiente' ?></td><td><?= e($r['estado']) ?></td><td><a href="?edit=<?= (int)$r['id'] ?>">Editar</a> <form class="inline" method="post" onsubmit="return confirm('¿Eliminar partido?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach; ?></table></div>
<h3>Estadísticas básicas</h3><ul><?php $total=(int)$pdo->query("SELECT COUNT(*) FROM partidos WHERE estado='Finalizado'")->fetchColumn(); $puntos=(int)$pdo->query("SELECT COALESCE(SUM(puntos_local+puntos_visitante),0) FROM partidos WHERE estado='Finalizado'")->fetchColumn(); ?><li>Partidos finalizados: <?= $total ?></li><li>Puntos anotados en total: <?= $puntos ?></li></ul>
<?php footer_html(); ?>
