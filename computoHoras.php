<?php
    session_start();
    
    $error=false;
    $mensajeError ="";
    $month = null;
    $resultados = [];
    
    if(!isset($_SESSION["id_trabajador"])){
        header("location: index.php");
        exit();
    }

    $id_trabajador = $_SESSION["id_trabajador"];
    $nombre = $_SESSION["nombre"];


    
    try{
        $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root");
        $conexion -> setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $month = htmlspecialchars($_POST["month"]);
            $sql= "SELECT id_trabajador, MIN(hora_inicio) AS hora_inicio_dia, MAX(hora_salida) AS hora_final_dia,
                SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(hora_salida, hora_inicio)))) AS tiempo_total, MONTH(fecha), observaciones,
                fecha FROM day_timetable WHERE id_trabajador = :id_trabajador AND MONTH(fecha) = :month GROUP BY fecha, id_trabajador ORDER BY fecha DESC" ;
            $stmt = $conexion -> prepare($sql);
            $stmt -> bindParam(':id_trabajador', $id_trabajador);
            $stmt -> bindParam(':month', $month);
            $stmt -> execute();

            $resultados = $stmt -> fetchAll(PDO:: FETCH_ASSOC);

        }

        else{
            $sql= "SELECT id_trabajador, MIN(hora_inicio) AS hora_inicio_dia, MAX(hora_salida) AS hora_final_dia,
                    SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(hora_salida, hora_inicio)))) AS tiempo_total, observaciones,
                    fecha FROM day_timetable WHERE id_trabajador = :id_trabajador GROUP BY fecha, id_trabajador ORDER BY fecha DESC" ;
            $stmt = $conexion -> prepare($sql);
            $stmt -> bindParam(':id_trabajador', $id_trabajador);
            $stmt -> execute();

            $resultados = $stmt -> fetchAll(PDO:: FETCH_ASSOC);
        }

    }catch(PDOException $e){
        $error=true;
        $mensajeError ="Error de conexión o consulta: " . $e->getMessage();
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
            <a href="/index.php"><svg height="50px" width="50px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.671 60.671" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <ellipse style="fill:#010002;" cx="30.336" cy="12.097" rx="11.997" ry="12.097"></ellipse> <path style="fill:#010002;" d="M35.64,30.079H25.031c-7.021,0-12.714,5.739-12.714,12.821v17.771h36.037V42.9 C48.354,35.818,42.661,30.079,35.64,30.079z"></path> </g> </g> </g></svg></a>
        </div>
    </header>

    <section>
        <h2 class="subheading">Cómputo de horas</h2>
        <a href="./exportar.php" target="_blank" class="exportarContainer">
            <button class="btnExportar"> 💾 Exportar a PDF</button>
        </a>
        <form action="" class="filterForm" method="post">
            <select name="month" id="month">
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembe</option>
            </select>
            <input type="submit" value="Filtrar">
        </form>
        <?php if(!empty($month)):?>
            <?php switch($month){
                                    case 1 :
                                        $month = "enero";
                                        break;
                                    case 2:
                                        $month = "febrero";
                                        break;
                                    case 3:
                                        $month = "marzo";
                                        break;
                                    case 4: 
                                        $month = "abril";
                                        break;
                                    case 5: 
                                        $month = "mayo";
                                        break;
                                    case 6: 
                                        $month = "junio";
                                        break;
                                    case 7: 
                                        $month = "julio";
                                        break;
                                    case 8: 
                                        $month = "agosto";
                                        break;
                                    case 9: 
                                        $month = "septiembre";
                                        break;
                                    case 10: 
                                        $month = "octubre";
                                        break;
                                    case 11: 
                                        $month = "noviembre";
                                        break;
                                    case 12: 
                                        $month = "diciembre";
                                        break;
            }?> 
            <h3>Cómputo de horas del mes de <?= $month?></h3>
            
            
        <?php endif;?>
        <table class="hoursCountTable">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora inicio</th>
                <th>Hora final</th>
                <th>Tiempo total</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody id="tableBody">
                <?php
                    if(count($resultados) > 0){
                        foreach($resultados as $fila){
                                switch($fila['observaciones']){
                                    case '0':
                                        $observaciones = "Sin observaciones";
                                        break;
                                    case '1':
                                        $observaciones = "Médico";
                                        break;
                                    case '2':
                                        $observaciones = "Vacaciones";
                                        break;
                                    case '3':
                                        $observaciones = "Baja";
                                        break;
                                    case '4':
                                        $observaciones = "Otros";
                                        break;
                                }
                            echo "<tr>";
                                echo "<td>" . htmlspecialchars($fila["fecha"]) . "</td>";
                                echo "<td>" . htmlspecialchars($fila["hora_inicio_dia"]) . "</td>";
                                echo "<td>" . htmlspecialchars($fila["hora_final_dia"]) . "</td>";
                                echo "<td>" . htmlspecialchars($fila["tiempo_total"]) . "</td>";
                                echo "<td>" . htmlspecialchars($observaciones) . "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td colspan='6'>No hay registros de horas para este trabajador.</td></tr>";
                    }
                ?>
        </tbody>
        </table>

        <input type="button" value="Cerrar sesión" class="cerrarSesion" onclick="location.href='logOut.php'">
    </section>
    <footer></footer>
    <script src="./script.js">showTable()</script>
</body>
</html>