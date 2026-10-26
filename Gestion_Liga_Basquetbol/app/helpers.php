<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}
function flash(string $message): void { $_SESSION['flash'] = $message; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        echo '<p class="flash">' . e($_SESSION['flash']) . '</p>';
        unset($_SESSION['flash']);
    }
}
function header_html(string $title): void {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . ' | Liga de Básquetbol</title><link rel="stylesheet" href="style.css"></head><body><header><h1>🏀 Liga de Básquetbol</h1><nav><a href="index.php">Inicio</a><a href="equipos.php">Equipos</a><a href="jugadores.php">Jugadores</a><a href="partidos.php">Partidos</a><a href="logout.php">Cerrar sesión</a></nav></header><main><h2>' . e($title) . '</h2>';
    show_flash();
}
function footer_html(): void { echo '</main><footer>Proyecto académico · Gestión de Liga de Básquetbol</footer></body></html>'; }
