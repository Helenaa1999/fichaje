<?php 
session_start();

$error = false;
$mensajeError ="";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error= true;
        echo "Por favor, completa todos los campos.";
        exit;
    }

    try {
        $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id_trabajador, nombre, password, rol FROM empleado WHERE email = :email";
        $stmt = $conexion->prepare($sql);
        $stmt -> bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        

        if ($stmt->rowCount() === 1) {
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $almacenada = $fila['password'];
            $rol = (int)$fila['rol'];
            $nombre = $fila['nombre'];
            $id= $fila['id_trabajador'];

            if(strlen($almacenada) <20 || !str_starts_with($almacenada, '$2y$')){
                $nuevoHash = password_hash($almacenada, PASSWORD_DEFAULT);
                $update = $conexion -> prepare ("UPDATE empleado SET password = :nuevo WHERE id_trabajador = :id");
                $update -> bindParam(':nuevo', $nuevoHash);
                $update -> bindParam(':id', $id);
                $update -> execute();
                $almacenada =  $nuevoHash;

            }
        }else{
            $error = true;
            echo "Usuario no encontrado.";
            exit();
        }

            if (password_verify($password, $almacenada)) {
                $_SESSION["email"] = $email;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["rol"] = $rol;
                
                if ($rol === 1) {
                    header("Location: indexManager.php");
                    exit();
                } elseif ($rol === 2) {
                    header("Location: fichaje.php");
                    exit();
                } else {
                    echo "Rol desconocido.";
                    exit();
                }
                exit();
            } else {
                $error = true;
                echo "Contraseña incorrecta.";
                exit();
            }

    } catch (PDOException $e) {
        $error = true;
        echo "Error de conexión o consulta: " . $e->getMessage();
    }
} else {
    $error = true;
    echo "Método de solicitud no válido.";
}
?>