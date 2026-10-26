# Gestión de Liga de Básquetbol

Aplicación web en PHP y MySQL para administrar equipos, jugadores y partidos de una liga.

## Requisitos
- PHP 8.1 o superior
- MySQL/MariaDB
- Apache (XAMPP recomendado)
- Git

## Instalación
1. Copia la carpeta `gestion_liga_basquetbol` a `htdocs` si usas XAMPP.
2. Crea la base de datos importando `database/schema.sql` desde phpMyAdmin.
3. Revisa `config/database.php` y configura usuario/contraseña de MySQL.
4. Abre `http://localhost/gestion_liga_basquetbol/public/`.
5. Crea el primer usuario desde `public/crear_usuario.php` y luego inicia sesión.
6. Por seguridad, elimina o renombra `public/crear_usuario.php` después de crear tu usuario.

## Módulos
- Autenticación con contraseñas cifradas.
- CRUD de equipos.
- CRUD de jugadores vinculados a equipos.
- Registro y listado de partidos.
- Resumen de partidos jugados y puntos anotados.

## Estructura
- `public/`: páginas visibles y punto de entrada.
- `app/`: conexión y funciones de apoyo.
- `config/`: configuración de base de datos.
- `database/`: estructura SQL.
- `doc/practica_liga.md`: documentación técnica.

## Control de versiones
Crea un repositorio Git y registra avances reales en diferentes sesiones. No concentres todos los commits al final:
```bash
git init
git add README.md database/
git commit -m "docs: agregar instrucciones y esquema inicial"
git add config/ app/
git commit -m "feat: preparar conexión y funciones comunes"
git add public/
git commit -m "feat: implementar módulos de la liga"
git add doc/
git commit -m "docs: agregar documentación técnica"
```
Estos comandos son una guía; realiza cada commit cuando termines realmente esa parte. Publica el repositorio en GitHub y pega su enlace en la entrega.
