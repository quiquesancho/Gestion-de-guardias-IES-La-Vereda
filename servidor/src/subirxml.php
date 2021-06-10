<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('status' => false));
    exit;
}

require 'dbhConnect.php';

$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json);

$type = $params->archivo;

$final = explode(',', $type);

if ($final[0] == 'data:text/xml;base64') {

    try {
        $dbh = ConexionDB::conectar();

        $result = base64_decode($final[1]);

        $xml = simplexml_load_string($result);

        if (!$xml) {
            echo json_encode(array('codigo' => 2));
        } else {

            $stmt = $dbh->prepare('delete from horariodocente');
            $stmt->execute();

            $stmt = $dbh->prepare('delete from horariogrupos');
            $stmt->execute();

            $stmt = $dbh->prepare('delete from docentes');
            $stmt->execute();

            $docentes = $xml->docentes;

            for ($i = 0; $i < count($docentes->docente); $i++) {

                $att = $docentes->docente[$i]->attributes();

                $nombre = (string)$att->nombre;
                $apellido1 = (string)$att->apellido1;
                $apellido2 = (string)$att->apellido2;
                $documento = (string)$att->documento;
                $email = (string)$att->correo;

                $stmt = $dbh->prepare("insert into docentes (email, dni, nombre, apellido1, apellido2) values (:email, :documento, :nombre, :apellido1, :apellido2)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':documento', $documento);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido1', $apellido1);
                $stmt->bindParam(':apellido2', $apellido2);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            }

            $horarioDocente = $xml->horarios_ocupaciones;

            for ($i = 0; $i < count($horarioDocente->horario_ocupacion); $i++) {

                $att = $horarioDocente->horario_ocupacion[$i]->attributes();

                $diaSemana = (string)$att->dia_semana;
                $horaDesde = (string)$att->hora_desde;
                $horaHasta = (string)$att->hora_hasta;
                $dni = (string)$att->documento;
                $ocupacion = (string)$att->ocupacion;


                $stmt = $dbh->prepare("insert into horariodocente (dia_semana, desde, hasta, dni_docente, ocupacion) values (:dia, :desde, :hasta, :dni, :ocupacion)");
                $stmt->bindParam(':dia', $diaSemana);
                $stmt->bindParam(':desde', $horaDesde);
                $stmt->bindParam(':hasta', $horaHasta);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':ocupacion', $ocupacion);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            }

            $horarioGrupo = $xml->horarios_grupo;
            for ($i = 0; $i < count($horarioGrupo->horario_grupo); $i++) {

                $att = $horarioGrupo->horario_grupo[$i]->attributes();

                $diaSemana = (string)$att->dia_semana;
                $horaDesde = (string)$att->hora_desde;
                $horaHasta = (string)$att->hora_hasta;
                $grupo = (string)$att->grupo;
                $dni = (string)$att->docente;
                $aula = (string)$att->aula;

                $stmt = $dbh->prepare("insert into horariogrupos (dia_semana, desde, hasta, grupo, dni_docente, aula) values (:dia, :desde, :hasta, :grupo, :dni, :aula)");
                $stmt->bindParam(':dia', $diaSemana);
                $stmt->bindParam(':desde', $horaDesde);
                $stmt->bindParam(':hasta', $horaHasta);
                $stmt->bindParam(':grupo', $grupo);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':aula', $aula);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            }

            echo json_encode(array('codigo' => 1));
        }
    } catch (Exception $e) {
        echo json_encode(array('codigo' => 2));
    }
} else {
    echo json_encode(array('codigo' => 0));
}
