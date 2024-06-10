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
                <h2 class="mb-4"  >Inicio sesión usuarios</h2>
                <p class="mb-4"> 
    

                <center>
				
		
		
                <?php
                echo '	<form method="post" action="inicio_user.php">

               
                            <table>

                            
                                <tr>
                                    <th>Documento</th>
                                    <th><input type="text" name="L_usuario" /></th>
                                </tr>
                                <tr>
                                    <th>contraseña</th>
                                    <th><input type="password" name="L_pass" /></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th><input name="login" type="submit" value="Inicio Sesion"  /></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </table>
                            
                            
                            <table style="width:100%">
                  <tr>
                    <th><a href="registro_usuario.php">Registro</a></th>
                    <th><a input href="index.html" type="button" >Regresar</a></th>
                  </tr>
                 </table>
                
                            </br>
                
                            </form>'; //Se define un formulario de inicio de sesión en HTML 
                
                            
                            
                if(isset($_POST['login'])){ //De acuerdo con el formulario definido aquí se ejecuta cuando presionamos el botón login 
                    $L_usuario=$_POST['L_usuario']; // Se guarda en una variable cada entrada definida en el formulario
                    $L_pass=md5($_POST['L_pass']); //Se guarda en una variable cada entrada definida en el formulario (codificamos la contraseña en MD5)
                    
                    if (!empty($L_usuario) && !empty($L_pass)){ // Se consulta que no exista ningún campo vacío
                        $sql =" SELECT usuario, contrasena, rol FROM usuarios WHERE usuario='$L_usuario';"; // Consulta de usuario en SQL
                        $resultado = pg_query($dbcon, $sql); // Se ejecuta la consulta en PostgreSQL con la conexión definida anteriormente
                        if($row = pg_fetch_array($resultado)){ // se estructura el resultado en matriz
                            if($row["contrasena"] == $L_pass){ // Valida la contraseña de la base de datos y la digitada por el usuario  
                               $_SESSION['usuario'] = $row['usuario']; //se define el parametro usuario en la sesion creada
                               $_SESSION['rol'] = $row['rol']; //se define el parametro usuario en la sesion creada
                                   echo '<script language="javascript">'; 
                                   echo 'location.href = "reportes_user.php";'; //se define el redireccionamiento de la pagina de inicio en javascript
                                   echo '</script>';			   
                            }else{
                               echo 'Contraseña Incorrecta'; // Si la contraseña de la base de datos no es igual a la digitada por el usuario, retorna un mensaje en HTML
                            }
                        }else{
                          echo 'Usuario no existente en la base de datos'; // Cuando la consulta en base de datos no retorna ningún valor, se debe a que no existe el usuario retornando un mensaje en HTML
                        }
                        
                    }else{
                        echo 'Inicio Sesión Fallido, Campos vacíos'; // Si existe algún campo vacío, retorna el mensaje en HTML
                    }
                }
                ?>
                
                </center>
                
            

            </div>
        </div>
    </div>



