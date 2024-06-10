<?php
        
include 'conexion_db_user.php'; // Se incluye el archivo de conexión a la base de datos
$dbcon = conexion(); // se crea una variable con la función definida anteriormente
session_start(); // se inicia una sesión
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0E1428">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <title>LUMIREPORT - SISTEMA DE REPORTE DE LUMINARIAS</title>




    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="./css/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./css/swiper-bundle.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <nav id="mainnav" class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container">

            <a class="navbar-brand" href="index.html">
                <img src="./images/menu_icon.png" alt="Logo" width="30" height="30"
                    class="d-inline-block align-text-top">
                LUMIREPORT
            </a>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
          

                <a class="btn btn-primary" href="#contact" role="button">Geovisor</a>

            </div>
        </div>
    </nav>


    <!-- hero section -->
    <div id="hero" class="py-6 d-md-py7">

        <div class="container " >
            <div id="insidehero" class="p-3  py-5 my-5 p-md-5 blurred-content" class="row justify-content-center">
                <h2 class="mb-4"  >Inicio sesión administración</h2>
                <p class="mb-4"> 
    

                <center>
		




	<form method="post" action="registro_usuario.php">
			<table>
				<tr>
					<th>Documento</th>
					<th><input type="text" name="R_usuario" /></th>
				</tr>
				<tr>
					<th>Nombre</th>
					<th><input type="text" name="R_nombre" /></th>
				</tr>
				<tr>
					<th>Apellidos</th>
					<th><input type="text" name="R_apellidos" /></th>
				</tr>
				<tr>
					<th>Contraseña</th>
					<th><input type="password" name="R_pass" /></th>
				</tr>
				<tr>
					<th></th>
					<th><input name="registro" type="submit" value="Registrar"  /></th>
				</tr>
			</table>
		</form>
<?php		
/* *************************    Inicio de la zona de registro   **************************/

if(isset($_POST['registro'])){ //De acuerdo con el formulario definido aquí se ejecuta cuando presionamos el botón registro 
    $R_usuario=$_POST['R_usuario']; // Se guarda en una variable cada entrada definida en el formulario
	$R_nombre=$_POST['R_nombre']; // Se guarda en una variable cada entrada definida en el formulario
	$R_apellidos=$_POST['R_apellidos']; // Se guarda en una variable cada entrada definida en el formulario
	$R_pass=md5($_POST['R_pass']); // Se guarda en una variable cada entrada definida en el formulario (codificamos la contraseña en MD5)
	
	if (!empty($R_usuario) && !empty($R_nombre) && !empty($R_apellidos) && !empty($R_pass)){ // Se consulta que no exista ningún campo vacío
		$sql ="INSERT INTO usuarios(usuario, nombre, apellidos,contrasena,rol) VALUES('$R_usuario', '$R_nombre', '$R_apellidos','$R_pass','usuario');"; // Ingreso de registro en SQL (parametros de usuario)
		$resultado = pg_query($dbcon, $sql); // Se ejecuta la consulta en PostgreSQL con la conexión definida anteriormente

		if(pg_affected_rows($resultado)==1){ //Si el registro es exitoso, retorna el valor de 1
			echo '<p>Registro exitoso</p>'; // Mensaje de salida en HTML
			echo '<p><a href="inicio_user.php">Inicio Sesion</a></p>'; // Mensaje de salida en HTML
		}else{
			echo 'Registro Fallido, Usuario no disponible'; // Si el registro no es exitoso, retorna el mensaje en HTML
		}	
	}else{
		echo 'Registro Fallido, Campos vacíos'; // Si existe algún campo vacío, retorna el mensaje en HTML
	}
}

?>

		 <!–-Se define un formulario de registro en HTML -->
		
</center>


            <section id="info">
                <h3>Debe registarse para acceder a los datos ambientales</h3>
                <div class="contenedor">
                  
                </div>
            </section>
            

            </div>
        </div>
    </div>



