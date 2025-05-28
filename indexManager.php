<?php
    session_start();
    
    if (!isset($_SESSION["id_trabajador"])) {
        header("Location: index.php");
        exit();
    }

    $idManager = $_SESSION["id_trabajador"];
    $nombreManager = $_SESSION["nombre"];
    try{
        $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id_trabajador, nombre, apellido1, apellido2, email, rol FROM empleado WHERE id_manager = :idManager";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':idManager', $idManager, PDO::PARAM_STR);
        $stmt->execute();

        $trabajadores= $stmt ->fetchAll(PDO::FETCH_ASSOC);



        if(isset($_POST["verTrabajador"])){
            $id_trabajador = htmlspecialchars($_POST["trabajador"]);
            foreach($trabajadores as $trabajador){
                if($trabajador["id_trabajador"] == $id_trabajador){
                    $nombre = $trabajador["nombre"];
                    $apellido1 = $trabajador["apellido1"];
                    $apellido2 = $trabajador["apellido2"];
                    $email = $trabajador["email"];
                    $rol = $trabajador["rol"];
                }
            }
        }

        if(isset($rol)){
            if($rol == 1){
                $rol = "Manager";
            }elseif($rol == 2){
                $rol = "Trabajador";
            }
        }

    }catch(PDOException $e){
        echo "Error de conexión o consulta: " . $e->getMessage();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/reloj.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Clock on</title>
</head>
<body>
    <header class ="header">
        <h1 class="title">Clock on</h1>
        <nav class="navContainer">
            <a href="fichaje.php">Fichaje</a>
            <a href="computoHoras.php">Cómputo de horas</a>
            <a href="indexManager.php">Lista trabajadores</a>
            <a href="fichajesTrabajadores.php">Fichajes trabajadores</a>
        </nav>
        <div>
            <a href="index.php"><svg height="50px" width="50px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.671 60.671" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <ellipse style="fill:#010002;" cx="30.336" cy="12.097" rx="11.997" ry="12.097"></ellipse> <path style="fill:#010002;" d="M35.64,30.079H25.031c-7.021,0-12.714,5.739-12.714,12.821v17.771h36.037V42.9 C48.354,35.818,42.661,30.079,35.64,30.079z"></path> </g> </g> </g></svg></a>
        </div>
    </header>
    <section class="elegirTrabajador">
        <form action="" class="formSelect" method="POST">
            <?php if(isset($nombreManager)) : ?>
                <h3><?=$nombreManager?> conectado</h3>
            <?php endif;?>
            <label for="verTrabajador">Elige un trabajador@:</label>
            <div class="selectContainer">
                <?php if(empty($trabajadores)):?>
                    <p>No hay trabajadores registrados</p>
                <?php else:?>
                    <select name="trabajador" id="trabajador">
                        <?php foreach($trabajadores as $trabajador):?>
                            <option value="<?= $trabajador['id_trabajador']?>">
                                <?= $trabajador['nombre'] . " " . $trabajador['apellido1'] . " " . $trabajador['apellido2']?>
                            </option>
                        <?php endforeach;?>
                    </select>
                <?php endif;?>
                <input type="submit" value="Ver trabajador@" name="verTrabajador">
            </div>
        </form>
        <?php if(isset($nombre)):?>
            <div class="workerData">
                <h2>Datos del trabajador@:</h2>
                <p><strong>Nombre:</strong> <?= $nombre?></p>
                <p><strong>Apellidos:</strong> <?= $apellido1 . " " . $apellido2?></p>
                <p><strong>Email:</strong> <?= $email?></p>
                <p><strong>Rol:</strong> <?= $rol?></p>
            </div>
        <?php endif;?>
        <div class="btnContainer">
            <a href="crearUsu.php" class="createWorkerBtn">Crear nuevo trabajador@</a>
            <input type="button" value="Cerrar sesión" class="cerrarSesion" onclick="location.href='logOut.php'">
        </div>
        

        
    </section>
    <footer></footer>
</body>
</html>