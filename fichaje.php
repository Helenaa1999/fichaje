<?php
session_start();

$mostrarInformacion = false;
$error = false;
$mensajeError = "";

if (!isset($_SESSION['email']) || !isset($_SESSION['id_trabajador'])) {
    header('Location: index.php');
    exit();
}

$idTrabajador = $_SESSION['id_trabajador'];
$nombreTrabajador = $_SESSION['nombre'];
$rolTrabajador = $_SESSION['rol'];


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["saveButton"])){

    if(empty(trim($_POST["dayChoosen"]))){
        $error=true;
        $mensajeError = "Por favor, introduce una fecha válida.";
    }else{
        $dayChoosen = htmlspecialchars($_POST["dayChoosen"]);
        $absenceOption = htmlspecialchars($_POST["absenceOptions"]);
        $clockOn1 = htmlspecialchars($_POST["clockOn1"]);
        $clockOut1 = htmlspecialchars($_POST["clockOut1"]); 
        $category1 = htmlspecialchars($_POST["category1"]);
        $location1 = htmlspecialchars($_POST["location1"]);
        $restBegins = htmlspecialchars($_POST["restBegins"]);
        $category2 = htmlspecialchars($_POST["category2"]);
        $location2 = htmlspecialchars($_POST["location2"]);
        $restEnds = htmlspecialchars($_POST["restEnds"]);
        $clockOn2 = htmlspecialchars($_POST["clockOn2"]);
        $clockOut2 = htmlspecialchars($_POST["clockOut2"]);
        $category3 = htmlspecialchars($_POST["category3"]);
        $location3 = htmlspecialchars($_POST["location3"]);

        $fechaFormateada = "";
        $dateObj = DateTime::createFromFormat('Y-m-d', $dayChoosen);

        if($dateObj){
            $fechaFormateada = $dateObj->format('Y-m-d');
        }else{
            $error=true;
            $mensajeError = "Formato de fecha incorrecto. Usa el formato dd/mm/aaaa.";
        }

        if(empty($fechaFormateada) || empty($clockOn1) || empty($clockOut1) || empty($category1) || empty($location1) || empty($restBegins) || empty($restEnds) || empty($clockOn2) || empty($clockOut2)){
            $error=true;
            $mensajeError = "Por favor, completa todos los campos obligatorios.";
        }else{
            if($clockOut1 <= $clockOn1 || $restEnds <= $restBegins || $clockOut2 <= $clockOn2){
                $error=true;
                $mensajeError = "La hora de salida debe ser mayor que la hora de entrada.";
            }else if (!$error){
                try{
                    $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
                    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $sql ="SELECT id_trabajador FROM day_timetable WHERE id_trabajador = :id_trabajador AND fecha = :fecha";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bindParam(':id_trabajador', $idTrabajador, PDO::PARAM_STR);
                    $stmt->bindParam(':fecha', $fechaFormateada, PDO::PARAM_STR);
                    $stmt->execute();
                    if($stmt->rowCount() > 0){
                        $error=true;
                        $mensajeError = "Ya existe un registro para este trabajador en esta fecha.";
                    }else {
                        $conexion->beginTransaction();

                        $sql = "INSERT INTO day_timetable (id_trabajador, hora_inicio, hora_salida, categoria, ubicacion, fecha, observaciones)
                                VALUES (:id_trabajador, :hora_inicio, :hora_salida, :categoria, :ubicacion, :fecha, :observaciones)";
                        $stmt = $conexion->prepare($sql);

                        $datos = [
                            [
                                'id_trabajador' => $idTrabajador,
                                'hora_inicio' => $clockOn1,
                                'hora_salida' => $clockOut1,
                                'categoria' => $category1,
                                'ubicacion' => $location1,
                                'fecha' => $fechaFormateada,
                                'observaciones' => $absenceOption
                            ],
                            [
                                'id_trabajador' => $idTrabajador,
                                'hora_inicio' => $restBegins,
                                'hora_salida' => $restEnds,
                                'categoria' => $category2,
                                'ubicacion' => $location2,
                                'fecha' => $fechaFormateada,
                                'observaciones' => $absenceOption
                            ],
                            [
                                'id_trabajador' => $idTrabajador,
                                'hora_inicio' => $clockOn2,
                                'hora_salida' => $clockOut2,
                                'categoria' => $category3,
                                'ubicacion' => $location3,
                                'fecha' => $fechaFormateada,
                                'observaciones' => $absenceOption
                            ]
                        ];

                        foreach ($datos as $dato) {
                            $stmt->execute([
                                ':id_trabajador' => $dato['id_trabajador'],
                                ':hora_inicio' => $dato['hora_inicio'],
                                ':hora_salida' => $dato['hora_salida'],
                                ':categoria' => $dato['categoria'],
                                ':ubicacion' => $dato['ubicacion'],
                                ':fecha' => $dato['fecha'],
                                ':observaciones' => $dato['observaciones']
                            ]);
                        }

                        $conexion->commit();

                        $mostrarInformacion = true;

                        if(isset($_POST["newRow"])){
                            $clockOn4 = htmlspecialchars($_POST["clockOn4"]);
                            $clockOut4 = htmlspecialchars($_POST["clockOut4"]);
                            $category4 = htmlspecialchars($_POST["category4"]);
                            $location4 = htmlspecialchars($_POST["location4"]);
                            
                            if(empty($clockOn4) || empty($clockOut4) || empty($category4) || empty($location4) && $clockOut4 <= $clockOn4){
                                $error=true;
                                $mensajeError = "Por favor, completa todos los campos obligatorios.";
                            }else{
                                $stmt -> execute([
                                    ':id_trabajador' => $idTrabajador,
                                    ':hora_inicio' => $clockOn4,
                                    ':hora_salida' => $clockOut4,
                                    ':categoria' => $category4,
                                    ':ubicacion' => $location4,
                                    ':fecha' => $fechaFormateada,
                                    ':observaciones' => $absenceOption
                                ]);
                            }
                        }
                    }
                } catch (PDOException $e) {
                    if (isset($conexion) && $conexion->inTransaction()) {
                        $conexion->rollBack();
                    }
                    $error = true;
                    $mensajeError = "Error de conexión o consulta: " . $e->getMessage();
                }
            }
        }
    }
    
    if(isset($_SESSION['success'])){
            $mostrarInformacion = true;
            unset($_SESSION['success']);
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
        </nav>
        <div>
            <a href="index.php"><svg height="50px" width="50px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.671 60.671" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <ellipse style="fill:#010002;" cx="30.336" cy="12.097" rx="11.997" ry="12.097"></ellipse> <path style="fill:#010002;" d="M35.64,30.079H25.031c-7.021,0-12.714,5.739-12.714,12.821v17.771h36.037V42.9 C48.354,35.818,42.661,30.079,35.64,30.079z"></path> </g> </g> </g></svg></a>
        </div>
    </header>

    <section>
        <div class="subheadingHoursContainer">
            <h2 class="subheading">Fichaje:</h2>
            <div class="hoursContainer">
                <p><strong>Jornada de hoy</strong></p>
                <p class="dayTime">00h <span>00m</span></p>
            </div>
        </div>
        
        <form action="" method="post">
            <div class="dayCommentsContainer">
                <div class="dayContainer">
                    <label for="day">Día:</label>
                    <input class="dayChoosen" type="date" id="dayChoosen" name="dayChoosen"/>
                </div>
                <div class="commentsContainer">
                    <label for="comments">Observaciones:</label>
                    <select name="absenceOptions" class="absenceOptionsSelect">
                        <option value="0"></option>
                        <option value="1"> 🟡 Vacaciones</option>
                        <option value="2"> 🔵 Médico</option>
                        <option value="3"> 🔴 Baja</option>
                        <option value="4"> 🟢 Otros</option>
                    </select>
                </div>
            </div>
            <div class="timetableContainer">
                <table id="table">
                    <thead>
                        <tr>
                            <th><label for="clockOn">Hora inicio</label></th>
                            <th><label for="clokOut">Hora final</label></th>
                            <th><label for="category">Categoría</label></th>
                            <th><label for="location">Ubicación</label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" id="clockOn1" name="clockOn1" class="time-picker" data-input required></td>
                            <td><input type="text" id="clockOut1" name="clockOut1" class="time-picker" data-input required></td>
                            <td>
                                <select class="categorySelect" name="category1">
                                    <option value="1" selected> 👨🏽‍💼 Trabajo</option>
                                    <option value="2"> ☕ Descanso</option>
                                </select>
                            </td>
                            <td>
                                <select class="locationSelect" name="location1">
                                    <option value="1"> 🏢 Oficina</option>
                                    <option value="2"> 🏠 Casa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" id="restBegins" name="restBegins" class="time-picker" data-input required></td>
                            <td><input type="text" id="restEnds" name="restEnds" class="time-picker" data-input required></td>
                            <td>
                                <select class="categorySelect" name="category2">
                                    <option value="1"> 👨🏽‍💼 Trabajo</option>
                                    <option value="2" selected> ☕ Descanso</option>
                                </select>
                            </td>
                            <td>
                                <select class="locationSelect" name="location2">
                                    <option value="1"> 🏢 Oficina</option>
                                    <option value="2"> 🏠 Casa</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td> <input type="text" id="clockOn2" name="clockOn2" class="time-picker" data-input required></td>
                            <td><input type="text" id="clockOut2" name="clockOut2" class="time-picker" data-input required></td>
                            <td>
                                <select class="categorySelect" name="category3">
                                    <option value="1" selected> 👨🏽‍💼 Trabajo</option>
                                    <option value="2"> ☕ Descanso</option>
                                </select>
                            </td>
                            <td>
                                <select class="locationSelect" name="location3">
                                    <option value="1"> 🏢 Oficina</option>
                                    <option value="2"> 🏠 Casa</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="buttonsContainer">
                    <input type="button" value="+ Añadir " class="addButton">
                    <input type="submit" value="Guardar" class="saveButton" name="saveButton">
                    
                    <?php if($mostrarInformacion):?>
                        <p class="success">Fichaje guardado correctamente</p>
                    <?php endif;?>

                    <?php if($error):?>
                        <p class="error"><?php echo $mensajeError;?></p>
                    <?php endif;?>
                </div>
            </div>
        </form>
        <div>
        <hr>
            <h3>Calendario de ausencias</h3>
            <form action="" class="absenceForm">
                <div class="absenceCalendar">
                    <div class="calendar">
                        <div id="calendar"></div>
                    </div>
                    <div class="legendContainer">
                        <p> 🟡 Vacaciones</p>
                        <p> 🔵 Médico</p>
                        <p> 🔴 Baja</p>
                        <p> 🟢 Otros</p>
                    </div>
                </div>
                <input type="submit" value="Enviar" class="btn">
            </form>
            <hr>
            <div class="absenceJustifyContainer">
                <h3>Adjuntar justificantes:</h4>
                <form action="" class="absenceJustifyForm">
                    <label for="date">Fecha del justificante:</label>
                    <input type="text" id="date" placeholder="Introduce la fecha" data-input required>
                    <label for="justify">Adjuntar justificante:</label>
                    <input type="file" id="justify" accept=".pdf, .jpg, .jpeg, .png" required>
                    <input type="submit" value="Enviar" class="btn">
                </form>
            </div>
        </div>
        <input type="button" value="Cerrar sesión" class="cerrarSesion" onclick="location.href='logOut.php'">
    </section>

    <footer></footer>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="./flatpickr.js"></script>
    <script src="./script.js"></script>
</body>
</html>