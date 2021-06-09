<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'dbhConnect.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    try {
        $fecha = new DateTime('now');
        $dia = obtenerDiaSemana();
        $hora = obtenerHoraGuardia();
        $dGuardia = array();

        $dbh = ConexionDB::conectar();

        $stmt = $dbh->prepare("select * from horariodocente where dia_semana = :dia and desde = :desde and ocupacion = '3249454'");
        $stmt->bindValue(':dia', $dia);
        $stmt->bindValue(':desde', $hora);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $docentes = $stmt->fetchAll();

        foreach ($docentes as $docente) {
            $stmt = $dbh->prepare("select * from registro where fecha = :fecha and hora = :hora and docente_guardia = :dni and docente_ausente is not null");
            $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
            $stmt->bindValue(':hora', $hora);
            $stmt->bindValue(':dni', $docente['dni_docente']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            if($stmt->rowCount() != 1){
                $stmt = $dbh->prepare("select * from docentes where dni = :dni");
                $stmt->bindValue(':dni',$docente['dni_docente']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                $d = $stmt->fetch();
                array_push($dGuardia, $d);
            }
        }

        echo json_encode($dGuardia);
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally {

        $dbh = null;
    }
}

function obtenerHoraGuardia()
{
    $date1 = new DateTime('now');
    $date2 = new DateTime('now');

    $date2->setTime(9, 10);
    if ($date1 < $date2) {
        return '8:15';
    }
    $date2->setTime(10, 5);
    if ($date1 < $date2) {
        return '9:10';
    }
    $date2->setTime(11, 0);
    if ($date1 < $date2) {
        return '10:05';
    }
    $date2->setTime(12, 25);
    if ($date1 < $date2) {
        return '11:30';
    }
    $date2->setTime(13, 20);
    if ($date1 < $date2) {
        return '12:25';
    }
    $date2->setTime(14, 15);
    if ($date1 < $date2) {
        return '13:20';
    }
    $date2->setTime(15, 10);
    if ($date1 < $date2) {
        return '14:15';
    } else {
        return '14:15';
    }
}

function obtenerDiaSemana()
{
    $dias = array(
        '1' => 'L',
        '2' => 'M',
        '3' => 'X',
        '4' => 'J',
        '5' => 'V'
    );

    $fecha = new DateTime('now');

    return $dias[$fecha->format('N')];
}
