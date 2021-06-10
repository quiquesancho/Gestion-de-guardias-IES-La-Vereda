<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'dbhConnect.php';

$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json);
$ldap = new LDAP();

$user = $params->user;
$pass = $params->pass;

$res = $ldap->login($user, $pass);

if ($res) {
    $mail = $ldap->getMail($user);

    try{
        $dbh = ConexionDB::conectar();
    $stmt = $dbh->prepare("select * from docentes where email = :mail");
    $stmt->bindValue(':mail', $mail[0]);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    $docente = $stmt->fetch();

    $docente['role'] = $ldap->getRole($user);

    $stmt = $dbh->prepare("select * from horariodocente where dia_semana = :dia and desde = :desde and dni_docente = :dni and ocupacion = 3249454");
    $stmt->bindValue(':dia', obtenerDiaSemana());
    $stmt->bindValue(':desde', obtenerHoraGuardia());
    $stmt->bindValue(':dni', $docente['dni']);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    $d = $stmt->fetchAll();

    if (count($d) == 1) {
        $docente['g'] = 'si';
        $fecha = new DateTime('now');
        $stmt = $dbh->prepare("select * from registro where fecha = :fecha and hora = :hora and docente_guardia = :dni");
        $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
        $stmt->bindValue(':hora', obtenerHoraGuardia());
        $stmt->bindValue(':dni', $docente['dni']);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $docente['c'] = 'no';
        } else {
            
            $docente['c'] = 'si';
        }
    } else {
        $docente['g'] = 'no';
        $docente['c'] = 'no';
    }


    echo json_encode($docente);
    }catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally{
        $dbh = null;
    }
} else {
    $response['message'] = 0;
    echo json_encode($response);
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
        '5' => 'V',
        '6' => 'V',
        '7' => 'V'
    );

    $fecha = new DateTime('now');

    return $dias[$fecha->format('N')];
}
