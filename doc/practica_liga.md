# Documentación técnica: Gestión de Liga de Básquetbol

## 1. Propósito y alcance

El sistema Gestión de Liga de Básquetbol es una aplicación web académica orientada a organizar la información principal de una liga deportiva. Su objetivo es centralizar el registro de equipos, jugadores y partidos, reducir la duplicación de datos y facilitar la consulta de los encuentros y sus marcadores. El sistema contempla autenticación básica de usuarios, operaciones de creación, consulta, actualización y eliminación, además de un resumen de estadísticas sencillas. Está dirigido a la persona encargada de mantener actualizada la información de la liga.

## 2. Arquitectura general

La solución utiliza una arquitectura web cliente-servidor. El navegador presenta las páginas HTML y los formularios; PHP procesa las solicitudes del usuario en el servidor; PDO conecta con MySQL o MariaDB para ejecutar consultas parametrizadas. La interfaz emplea CSS propio, sin dependencias de bibliotecas externas. La estructura separa los archivos públicos de la configuración, las funciones comunes, el esquema de base de datos y la documentación. En un despliegue real, la configuración sensible debe mantenerse fuera del directorio público y el sitio debe servirse mediante HTTPS.

## 3. Organización de archivos, clases y componentes

El proyecto se organiza en las siguientes carpetas:

- `public/`: contiene `login.php`, `logout.php`, `index.php`, `equipos.php`, `jugadores.php`, `partidos.php`, `crear_usuario.php` y `style.css`. Estos archivos gestionan la autenticación, la navegación y los formularios de los módulos.
- `config/database.php`: crea la conexión PDO a la base de datos y configura el manejo de errores y las consultas preparadas.
- `app/helpers.php`: reúne funciones reutilizables para escapar texto HTML, validar la sesión, redirigir, mostrar mensajes y generar la cabecera y el pie de página.
- `database/schema.sql`: crea las tablas y define sus relaciones.
- `doc/practica_liga.md`: describe la arquitectura, funcionalidades y decisiones técnicas.

En esta implementación PHP no se definen clases ni controladores de Laravel; las páginas PHP cumplen el papel de controladores sencillos y las tablas de MySQL almacenan los modelos de información. Laravel puede incorporarse posteriormente para organizar rutas, controladores, modelos Eloquent, validación y migraciones siguiendo el patrón MVC.

## 4. Modelo de datos

La tabla `usuarios` almacena nombre, correo único y hash de contraseña. `equipos` contiene nombre, ciudad y entrenador. `jugadores` registra nombres, apellidos, dorsal, posición, edad y una clave foránea hacia el equipo. La combinación equipo-dorsal es única para evitar números repetidos dentro del mismo equipo. `partidos` relaciona un equipo local con uno visitante e incluye fecha, marcador y estado. Las claves foráneas preservan la integridad referencial; eliminar un equipo elimina también sus jugadores y partidos asociados. Cada encuentro debe enfrentar a dos equipos diferentes.

## 5. Funcionalidades implementadas

La autenticación permite crear una cuenta e iniciar sesión. La contraseña se guarda mediante `password_hash` y se verifica con `password_verify`; al iniciar sesión se regenera el identificador de sesión. El módulo de equipos permite registrar, editar, listar y eliminar equipos, además de contar sus jugadores. El módulo de jugadores permite asignar cada jugador a un equipo y mantener su dorsal y posición. El módulo de partidos registra encuentros programados o finalizados, permite editar el marcador y muestra los resultados. El resumen calcula el número de partidos finalizados y los puntos anotados entre ambos equipos. Los formularios usan consultas preparadas para reducir el riesgo de inyección SQL y el texto mostrado se escapa con `htmlspecialchars`.

## 6. Fragmentos de código relevantes

La conexión utiliza PDO y consultas preparadas. Por ejemplo, para localizar una cuenta se usa una consulta con parámetro en vez de concatenar el correo en SQL. La contraseña no se almacena en texto plano: `password_hash($password, PASSWORD_DEFAULT)` produce el hash y `password_verify` comprueba la contraseña durante el acceso. En el módulo de jugadores, una clave foránea vincula el registro con un equipo existente. Estas decisiones mejoran la seguridad y la consistencia de los datos.

## 7. Problemas previstos y soluciones

Entre los problemas habituales están una conexión fallida por credenciales incorrectas, un correo de usuario duplicado, un dorsal repetido dentro del mismo equipo y la selección de un equipo inexistente. Se atienden configurando correctamente la conexión, utilizando restricciones únicas y claves foráneas, y mostrando mensajes comprensibles cuando una operación no se completa. También se valida que un partido no enfrente al mismo equipo consigo mismo y que los marcadores se registren cuando el encuentro está finalizado.

## 8. Decisiones técnicas y mejoras futuras

Se eligió PHP con PDO y MySQL por su disponibilidad en entornos académicos como XAMPP. Se utilizó CSS propio para mantener la interfaz ligera y adaptable. Como mejora, se recomienda migrar a Laravel si el curso requiere practicar explícitamente ese framework; añadir roles y permisos, token CSRF en formularios, paginación, pruebas automatizadas, validación centralizada y despliegue HTTPS. El archivo de creación de usuario debe eliminarse o restringirse después de crear la cuenta inicial. Para la entrega, el historial Git debe reflejar avances reales en diferentes fechas y el repositorio remoto debe compartirse con el docente.
