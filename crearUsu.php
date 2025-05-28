<?php
    session_start();
    $mostrarResultado = false;
    if($_SERVER["REQUEST_METHOD"]== "POST"){
        $name = htmlspecialchars($_POST["workerName"]);
        $apellido1 = htmlspecialchars($_POST["surname1"]);
        $apellido2 = htmlspecialchars($_POST["surname2"]);
        $rolInput = htmlspecialchars($_POST["rol"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        if(!preg_match('/^(?=.*\d).{8,}$/', $password)){
            $error ="La contraseña debe contener al menos 8 caracteres y al menos un número";
        }

        $rol = intval($rolInput);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try{

            $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
            $conexion -> setAttribute(PDO:: ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql ="INSERT INTO empleado (nombre, apellido1, apellido2, rol, email, password, id_manager)
                    VALUES (:nombre, :apellido1, :apellido2, :rol, :email, :password, :id_manager)";
            $stmt = $conexion -> prepare($sql);

            $stmt -> bindParam(':nombre', $name);
            $stmt -> bindParam(':apellido1', $apellido1);
            $stmt -> bindParam(':apellido2', $apellido2);
            $stmt -> bindParam(':rol', $rol);
            $stmt -> bindParam(':email', $email);
            $stmt -> bindParam(':password', $hashedPassword);
            $stmt -> bindParam(':id_manager', $_SESSION["id_trabajador"]);

            $stmt -> execute();

            $mostrarResultado = true;
        }catch(PDOException $e){
            echo "Error de conexión o consulta: " . $e ->getMessage();
        }
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
            <a href="#">Fichajes trabajadores</a>
        </nav>
        <div>
            <a href="/index.html"><svg height="50px" width="50px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.671 60.671" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <ellipse style="fill:#010002;" cx="30.336" cy="12.097" rx="11.997" ry="12.097"></ellipse> <path style="fill:#010002;" d="M35.64,30.079H25.031c-7.021,0-12.714,5.739-12.714,12.821v17.771h36.037V42.9 C48.354,35.818,42.661,30.079,35.64,30.079z"></path> </g> </g> </g></svg></a>
        </div>
    </header>

    <section>
        <h1>Crear nuevo trabajador@</h1>
        <p>Rellena el siguiente formulario para crear un nuevo trabajador@</p>
        <div class="formContainer">
            <form class="formNewWorker" action="" method="POST">
                <div>
                    <label for="workerName">Nombre:</label>
                    <input type="text" name="workerName" placeholder="Introduce el nombre del trabajador@" required>
                </div>
                <div>
                    <label for="surname1">Apellido 1:</label>
                    <input type="text" name="surname1" placeholder="Introduce el primer apellido" required>
                </div>
                <div>
                    <label for="surname2">Apellido 2:</label>
                    <input type="text" name="surname2" placeholder="Introduce el segundo apellido" required>
                </div>
                <div>
                    <label for="rol">Rol:</label>
                    <select name="rol" id="rol">
                        <option value="2">Junior</option>
                        <option value="1">Manager</option>
                    </select>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Crea un email para el trabajador@" required>
                </div>
                <div>
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" placeholder="Crea una contraseña para el trabajador@" required>
                    <?php if(!empty($error)):?>
                        <p class="error"><?php echo $error; ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <p>Debe contener:</p>
                    <ul>
                        <li>Mínimo 8 caracteres</li>
                        <li>Mínimo un número</li>
                    </ul>
                </div>
                
                <input type="submit" value="Crear nuevo trabajador">
            </form>
        </div>
        
        <?php if ($mostrarResultado): ?>
            <p class="success">Trabajador creado correctamente</p>
        <?php endif; ?>
    </section>
    <footer></footer>

    <script>
    document.querySelector("form").addEventListener("submit", function(e){
        const password=document.querySelector('input[name="password"]').value;
        const regex = /^(?=.*\d).{8,}$/;

        if(!regex.test(password)){
            e.preventDefault();
            alert("La contraseña debe contener al menos 8 caracteres y al menos un número.");
        }
    });
    </script> 
</body>
</html>