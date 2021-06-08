<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$method = $_SERVER['REQUEST_METHOD'];

require 'dbhConnect.php';

if ($method == 'GET') {

    try {

        $users = array();

        $dbh = ConexionDb::conectar();

        $stmt = $dbh->prepare("select * from horariodocente where ocupacion = 3249454");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $results = $stmt->fetchAll();

        foreach ($results as $row) {

            $stmt = $dbh->prepare("select * from docentes where dni = :dni");
            $stmt->bindParam(':dni', $row['dni_docente']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $docente = $stmt->fetch();

            $hora = explode(":", $row['desde']);

            $result = array(
                'email' => $docente['email'],
                'dni' => $row['dni_docente'],
                'nombre' => $docente['nombre'],
                'apellido1' => $docente['apellido1'],
                'apellido2' => $docente['apellido2'],
                'diaGuardia' => $row['dia_semana'],
                'horaGuardia' => "{$hora[0]}:{$hora[1]}"
            );

            array_push($users, $result);
        }

        echo json_encode($users);
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally {
        $dbh = null;
    }
}

if ($method == 'POST') {

    try {
        $users = array();

        $dbh = ConexionDb::conectar();

        $stmt = $dbh->prepare("select * from docentes order by apellido1");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $docente = $stmt->fetchAll();

        foreach ($docente as $docente) {
            $user = array(
                'email' => $docente['email'],
                'dni' => $docente['dni'],
                'nombre' => $docente['nombre'],
                'apellido1' => $docente['apellido1'],
                'apellido2' => $docente['apellido2'],
            );

            array_push($users, $user);
        }

        echo json_encode($users);
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally {
        $dbh = null;
    }
}
