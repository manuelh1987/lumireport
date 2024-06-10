<?php
session_start(); // Se inicia una sesión
session_destroy(); // Se destruye la sesión
echo 'Ha terminado la sesión.'; // Mensaje en HTML
echo '<p>Redirigiendo a <a href="index.html">Inicio</a></p>'; // Enlace de redirección en HTML
header('Location: index.html'); // Redirige al usuario a index.html
exit(); // Finaliza el script después de la redirección
?>