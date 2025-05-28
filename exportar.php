<?php
    require('fpdf/fpdf.php');
    session_start();
    $id_trabajador = $_SESSION["id_trabajador"];

    $conexion = new PDO("mysql:host=localhost;dbname=fichaje;charset=utf8mb4", "root", "");
    $conexion -> setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
    $sql= "SELECT id_trabajador, MIN(hora_inicio) AS hora_inicio_dia, MAX(hora_salida) AS hora_final_dia,
            SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(hora_salida, hora_inicio)))) AS tiempo_total, observaciones,  
            fecha FROM day_timetable WHERE id_trabajador = :id_trabajador GROUP BY fecha, id_trabajador ORDER BY fecha DESC" ;
    $stmt = $conexion -> prepare($sql);
    $stmt -> bindParam(':id_trabajador', $id_trabajador);
    $stmt -> execute();
    $resultados = $stmt -> fetchAll(PDO:: FETCH_ASSOC);
    

    class PDF extends FPDF{
        function Header (){
            $this -> setFont('Arial', 'B', 12);
            $this -> Cell(0, 10, utf8_decode('Cómputo de horas por días'), 0, 1, 'C');
            $this -> Ln(5);
        }
        function BasicTable($header, $data){
            foreach($header as $col){
                $this -> Cell(35, 10, $col, 1);
            }
            $this -> Ln();
            $this -> SetFont('Arial', '', 12);
            foreach($data as $row){
                $this -> Cell(35, 10, $row['fecha'], 1);
                $this -> Cell(35, 10, $row['hora_inicio_dia'], 1);
                $this -> Cell(35, 10, $row['hora_final_dia'], 1);
                $this -> Cell(35, 10, $row['tiempo_total'], 1);
                $this -> Cell(35, 10, $row['observaciones'], 1);
                $this -> Ln();
            }
        }
    }

    $pdf = new PDF();
    $header = array('Fecha', 'Hora inicio', 'Hora salida', 'Tiempo total', 'Observaciones');
    $pdf -> AddPage();
    $pdf -> BasicTable($header, $resultados);
    $pdf -> Output('D', 'computoHoras.pdf');
?>