<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: *');

$method = $_SERVER['REQUEST_METHOD'];

require 'dbhConnect.php';

$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json);

if ($method == 'GET') {

    try {

        $fecha = new DateTime('now');
        $guardias = array();
        $hora = obtenerHoraGuardia();

        $dbh = ConexionDB::conectar();

        $stmt = $dbh->prepare("select * from docente_ausente where fecha = :fecha and (hora = :hora or hora = '00:00')");
        $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
        $stmt->bindValue(':hora', $hora);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $faltas = $stmt->fetchAll();

        foreach ($faltas as $falta) {
            $stmt = $dbh->prepare("select * from docentes where dni = :dni");
            $stmt->bindValue(':dni', $falta['dni_docente']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $docente = $stmt->fetch();

            $diaSemana = obtenerDiaSemana();
            if ($falta['hora'] == '00:00:00') {
                $hora = obtenerHoraGuardia();
            } else {
                $hora = $falta['hora'];
            }

            $stmt = $dbh->prepare("select * from horariogrupos where dia_semana = :dia and desde = :hora and dni_docente = :dni");
            $stmt->bindValue(':dia', $diaSemana);
            $stmt->bindValue(':hora', $hora);
            $stmt->bindValue(':dni', $falta['dni_docente']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $grupo = $stmt->fetch();


            $guardia = array(
                'fecha' => $fecha->format('d-m-Y'),
                'hora' => $hora,
                'dAusente' => $docente,
                'aula' => $grupo['aula'],
                'grupo' => $grupo['grupo'],
                'observacion' => ''
            );

            array_push($guardias, $guardia);
        }

        $stmt = $dbh->prepare("select * from registro where fecha = :dia and hora = :hora and docente_ausente is not null");
        $stmt->bindValue(':dia', $fecha->format('Y-m-d'));
        $stmt->bindValue(':hora', $hora);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $registro = $stmt->fetchAll();

        if (count($registro) > 0) {
            foreach ($registro as $r) {

                $stmt = $dbh->prepare("select * from docentes where dni = :dni");
                $stmt->bindValue(':dni', $r['docente_ausente']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                $docenteAusente = $stmt->fetch();

                $stmt = $dbh->prepare("select * from docentes where dni = :dni");
                $stmt->bindValue(':dni', $r['docente_guardia']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                $docenteGuardia  = $stmt->fetch();

                $diaSemana = obtenerDiaSemana();
                if ($r['hora'] == '00:00') {
                    $hora = obtenerHoraGuardia();
                } else {
                    $hora = $r['hora'];
                }

                $stmt = $dbh->prepare("select * from horariogrupos where dia_semana = :dia and desde = :hora and dni_docente = :dni");
                $stmt->bindValue(':dia', $diaSemana);
                $stmt->bindValue(':hora', $hora);
                $stmt->bindValue(':dni', $r['docente_ausente']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                $grupo = $stmt->fetch();

                $guardia = array(
                    'fecha' => $fecha->format('d-m-Y'),
                    'hora' => $hora,
                    'dAusente' => $docenteAusente,
                    'dGuardia' => $docenteGuardia,
                    'aula' => $grupo['aula'],
                    'grupo' => $grupo['grupo'],
                    'observacion' => ''
                );

                array_push($guardias, $guardia);
            }
        }


        echo json_encode($guardias);
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally {
        $dbh = null;
    }
}

if ($method == 'POST') {
    $action = $params->action;

    if ($action == 'confirm') {
        try {

            $email = $params->email;
            $fecha = new DateTime('now');

            $dbh = ConexionDB::conectar();

            $stmt = $dbh->prepare('select * from docentes where email = :email');
            $stmt->bindValue(':email', $email);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $docente = $stmt->fetch();

            $stmt = $dbh->prepare("insert into registro (fecha, hora, docente_guardia, observaciones) values (:fecha, :hora, :dni, 'Guardia confirmada
            -----------------------------------------
            ')");
            $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
            $stmt->bindValue(':hora', obtenerHoraGuardia());
            $stmt->bindValue(':dni', $docente['dni']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            echo json_encode(array('codigo' => '1'));
        } catch (PDOException $e) {
            echo json_encode($e->getMessage());
        } finally {
            $dbh = null;
        }
    }

    if ($action == 'tieneGuardia') {
        try {

            $email = $params->email;
            $fecha = new DateTime('now');

            $dbh = ConexionDB::conectar();

            $stmt = $dbh->prepare('select * from docentes where email = :email');
            $stmt->bindValue(':email', $email);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $dGuardia = $stmt->fetch();

            $stmt = $dbh->prepare("select * from registro where fecha = :fecha and hora = :hora and docente_guardia = :dni and docente_ausente is not null");
            $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
            $stmt->bindValue(':hora', obtenerHoraGuardia());
            $stmt->bindValue(':dni', $dGuardia['dni']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {

                $registro = $stmt->fetch();

                $stmt = $dbh->prepare('select * from docentes where dni = :dni');
                $stmt->bindValue(':dni', $registro['docente_ausente']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                $dAusente = $stmt->fetch();

                $stmt = $dbh->prepare('select * from horariogrupos where dia_semana = :dia and desde = :hora and dni_docente = :dni');
                $stmt->bindValue(':dia', obtenerDiaSemana());
                $stmt->bindValue(':hora', obtenerHoraGuardia() . ':00');
                $stmt->bindValue(':dni', $dAusente['dni']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                $hGrupo = $stmt->fetch();

                $guardia = array(
                    'fecha' => $fecha->format('Y-m-d'),
                    'hora' => obtenerHoraGuardia(),
                    'dGuardia' => $dGuardia,
                    'dAusente' => $dAusente,
                    'aula' => $hGrupo['aula'],
                    'grupo' => $hGrupo['grupo'],
                    'observacion' => $registro['observaciones']
                );

                echo json_encode(array('guardia' => $guardia, 'codigo' => '1'));
            } else {
                echo json_encode(array('codigo' => '0'));
            }
        } catch (PDOException $e) {
            echo json_encode($e->getMessage());
        } finally {
            $dbh = null;
        }
    }

    if ($action == 'asignarGuardia') {
        $email = $params->email;
        $guardia = $params->guardia;

        $fecha = new DateTime($params->guardia->fecha);

        try {
            $dbh = ConexionDB::conectar();

            $stmt = $dbh->prepare('select * from docentes where email = :email');
            $stmt->bindValue(':email', $email);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $dGuardia = $stmt->fetch();

            $stmt = $dbh->prepare('select * from docentes where email = :email');
            $stmt->bindValue(':email', $guardia->dAusente->email);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $dAusente = $stmt->fetch();

            $stmt = $dbh->prepare('select * from registro where fecha = :fecha and hora = :hora and docente_guardia = :dGuardia');
            $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
            $stmt->bindValue(':hora', $params->guardia->hora);
            $stmt->bindValue(':dGuardia', $dGuardia['dni']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $stmt = $dbh->prepare('update registro set docente_ausente = :dAusente, grupo = :grupo, aula = :aula where fecha = :fecha and hora = :hora and docente_guardia = :dGuardia');
                $stmt->bindValue(':dAusente', $dAusente['dni']);
                $stmt->bindValue(':grupo', $params->guardia->grupo);
                $stmt->bindValue(':aula', $params->guardia->aula);
                $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
                $stmt->bindValue(':hora', $params->guardia->hora);
                $stmt->bindValue(':dGuardia', $dGuardia['dni']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            } else {
                $stmt = $dbh->prepare("insert into registro(docente_ausente, grupo, aula, fecha, hora, docente_guardia, observaciones) values (:dAusente, :grupo, :aula, :fecha, :hora, :dGuardia,'Guardia confirmada
                -----------------------------------------
                ')");
                $stmt->bindValue(':dAusente', $dAusente['dni']);
                $stmt->bindValue(':grupo', $params->guardia->grupo);
                $stmt->bindValue(':aula', $params->guardia->aula);
                $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
                $stmt->bindValue(':hora', $params->guardia->hora);
                $stmt->bindValue(':dGuardia', $dGuardia['dni']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            }

            $stmt = $dbh->prepare('select * from docente_ausente where dni_docente = :dni and fecha = :fecha');
            $stmt->bindValue(':dni', $dAusente['dni']);
            $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $falta = $stmt->fetch();

            if ($falta['hora'] == '00:00:00') {
                if (obtenerHoraGuardia() == '14:15') {
                    $stmt = $dbh->prepare("delete from docente_ausente where dni_docente = :dni and fecha = :fecha and hora = '00:00:00'");
                    $stmt->bindValue(':dni', $dAusente['dni']);
                    $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $stmt->execute();
                }
            } else {
                $stmt = $dbh->prepare("delete from docente_ausente where dni_docente = :dni and fecha = :fecha and hora = :hora");
                $stmt->bindValue(':dni', $dAusente['dni']);
                $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
                $stmt->bindValue(':hora', $params->guardia->hora);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
            }


            echo json_encode(array('codigo' => '1'));
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
        } finally {
            $dbu = null;
        }
    }
}

if ($method == 'PUT') {
    $guardia = $params->guardia;

    try {
        $dbh = ConexionDB::conectar();

        $stmt = $dbh->prepare('update registro set observaciones = :obser where fecha = :fecha and hora = :hora and docente_guardia = :dGuardia');
        $stmt->bindValue(':obser', $guardia->observacion);
        $stmt->bindValue(':fecha', $guardia->fecha);
        $stmt->bindValue(':hora', $guardia->hora);
        $stmt->bindValue(':dGuardia', $guardia->dGuardia->dni);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        echo json_encode(array('codigo' => '1'));
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
        '5' => 'V',
        '6' => 'V',
        '7' => 'V'
    );

    $fecha = new DateTime('now');

    return $dias[$fecha->format('N')];
}
