<?php
session_start();
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $id_trabajador = htmlspecialchars($_POST["workerCode"]);

    if(empty($id_trabajador)){
        echo "<p>El código de trabajador no puede estar vacío</p>";
        exit;
    }

    try{
        $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id_trabajador, nombre, rol FROM empleado where id_trabajador = :id_trabajador";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_trabajador', $id_trabajador, PDO::PARAM_STR);
        $stmt ->execute();

        $fila = $stmt -> fetch(PDO::FETCH_ASSOC);
        $rol = $fila["rol"];
        $nombre = $fila["nombre"];

        if($stmt ->rowCount() ===1){
            $_SESSION["id_trabajador"] = $id_trabajador;
            $_SESSION["nombre"] = $nombre;

            if($rol == 1){
                header("Location: indexManager.php");
            }elseif($rol == 2){
                header("Location: fichaje.php");
            }else{
                echo "Rol desconocido.";
            }
            exit();  
        }
    }catch(PDOException $e){
        echo "Error de conexión o consulta: " . $e->getMessage();
    }
}

?>