<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers.php';
require_login();
$equipos = (int)$pdo->query('SELECT COUNT(*) FROM equipos')->fetchColumn();
$jugadores = (int)$pdo->query('SELECT COUNT(*) FROM jugadores')->fetchColumn();
$partidos = (int)$pdo->query('SELECT COUNT(*) FROM partidos')->fetchColumn();
$finalizados = (int)$pdo->query("SELECT COUNT(*) FROM partidos WHERE estado='Finalizado'")->fetchColumn();
header_html('Panel principal');
?>
<p>Bienvenido, <strong><?= e($_SESSION['nombre'] ?? 'usuario') ?></strong>. Desde este panel puedes administrar la liga.</p>
<section class="cards">
<div class="card"><span>Equipos</span><strong><?= $equipos ?></strong><a href="equipos.php">Administrar</a></div>
<div class="card"><span>Jugadores</span><strong><?= $jugadores ?></strong><a href="jugadores.php">Administrar</a></div>
<div class="card"><span>Partidos</span><strong><?= $partidos ?></strong><a href="partidos.php">Administrar</a></div>
<div class="card"><span>Finalizados</span><strong><?= $finalizados ?></strong><span>partidos</span></div>
</section>
<h3>¿Qué puedes hacer?</h3><ul><li>Registrar y mantener equipos.</li><li>Asignar jugadores y números de camiseta.</li><li>Programar encuentros y registrar marcadores.</li><li>Consultar resultados de partidos finalizados.</li></ul>
<?php footer_html(); ?>
