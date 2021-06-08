<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'dbhConnect.php';

$json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR

$params = json_decode($json);
$method = $params->method;

if ($method == 'get') {
    $mail = $params->mail;

    if ($mail == 'all') {

        try {
            $dbh = ConexionDB::conectar();

            $stmt = $dbh->prepare("select * from docentes");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $faltas = array();

            $docentes = $stmt->fetchAll();

            foreach ($docentes as $docente) {
                $stmt = $dbh->prepare("select * from docente_ausente where dni_docente = :dni order by fecha");
                $stmt->bindValue(':dni', $docente['dni']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();


                while ($row = $stmt->fetch()) {
                    $date = new DateTime($row['fecha']);

                    if ($row['hora'] == '00:00:00') {
                        $hora = 'Todo el día';
                    } else {
                        $timeSplit = explode(':', $row['hora']);
                        $date->setTime($timeSplit[0], $timeSplit[1]);
                        $hora = $date->format('H:i');
                    }

                    $falta = array(
                        'email' => $docente['email'],
                        'fecha' => $date->format('d-m-Y'),
                        'hora' => $hora,
                        'user' => array(
                            'email' => $docente['email'],
                            'dni' => $docente['dni'],
                            'nombre' => $docente['nombre'],
                            'apellido1' => $docente['apellido1'],
                            'apellido2' => $docente['apellido2']
                        )
                    );
                    array_push($faltas, $falta);
                }
            }

            echo json_encode($faltas);
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
        } finally {
            $dbh = null;
        }
    } else {

        try {
            $dbh = ConexionDB::conectar();

            $stmt = $dbh->prepare("select * from docentes where email = :mail");
            $stmt->bindValue(':mail', $mail);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $docente = $stmt->fetch();

            $stmt = $dbh->prepare("select * from docente_ausente where dni_docente = :dni order by fecha");
            $stmt->bindValue(':dni', $docente['dni']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $faltas = array();

            while ($row = $stmt->fetch()) {
                $date = new DateTime($row['fecha']);
                if ($row['hora'] == '00:00:00') {
                    $hora = 'Todo el día';
                } else {
                    $timeSplit = explode(':', $row['hora']);
                    $date->setTime($timeSplit[0], $timeSplit[1]);
                    $hora = $date->format('H:i');
                }
                $falta = array(
                    'email' => $mail,
                    'fecha' => $date->format('d-m-Y'),
                    'hora' => $hora,
                    'user' => array(
                        'email' => $docente['email'],
                        'dni' => $docente['dni'],
                        'nombre' => $docente['nombre'],
                        'apellido1' => $docente['apellido1'],
                        'apellido2' => $docente['apellido2']
                    )
                );
                array_push($faltas, $falta);
            }

            echo json_encode($faltas);
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
        } finally {
            $dbh = null;
        }
    }
}

if ($method == 'add') {
    $mail = $params->falta->email;
    $fecha = $params->falta->fecha;
    $hora = $params->falta->hora;

    $auxDate = new DateTime('now');
    $correctDay = compararFechas($fecha, $auxDate->format('Y-m-d'));



    if ($hora == '00:00') {

        if ($correctDay > 0 || ($correctDay == 0 && obtenerHoraGuardia() == '8:15')) {
            try {
                $dbh = ConexionDB::conectar();

                $stmt = $dbh->prepare("select * from docentes where email = :mail");
                $stmt->bindValue(':mail', $mail);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                $dni = $stmt->fetch();

                $stmt = $dbh->prepare("select * from docente_ausente where dni_docente = :dni and fecha = :fecha and hora = :hora");
                $stmt->bindValue(':dni', $dni['dni']);
                $stmt->bindValue(':fecha', $fecha);
                $stmt->bindValue(':hora', $hora);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {

                    $stmt = $dbh->prepare("insert into docente_ausente (dni_docente, fecha, hora) values (:dni, :fecha, :hora)");
                    $stmt->bindValue(':dni', $dni['dni']);
                    $stmt->bindValue(':fecha', $fecha);
                    $stmt->bindValue(':hora', $hora);
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $stmt->execute();

                    echo json_encode(array('codigo' => 1));
                } else {
                    echo json_encode(array('codigo' => 2));
                }
            } catch (Exception $e) {
                echo json_encode($e->getMessage());
            } finally {
                $dbh = null;
            }
        } else {
            echo json_encode(array('codigo' => 0));
        }
    } else {

        if ($correctDay > 0) {
            $correctHour = true;
        } else {
            $correctHour = comprobarHora($hora);
        }

        if ($correctDay >= 0 && $correctHour) {

            try {
                $dbh = ConexionDB::conectar();

                $stmt = $dbh->prepare("select * from docentes where email = :mail");
                $stmt->bindValue(':mail', $mail);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                $dni = $stmt->fetchAll();

                $stmt = $dbh->prepare("select * from docente_ausente where dni_docente = :dni and fecha = :fecha and hora = :hora");
                $stmt->bindValue(':dni', $dni[0]['dni']);
                $stmt->bindValue(':fecha', $fecha);
                $stmt->bindValue(':hora', $hora);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {

                    $stmt = $dbh->prepare("insert into docente_ausente (dni_docente, fecha, hora) values (:dni, :fecha, :hora)");
                    $stmt->bindValue(':dni', $dni[0]['dni']);
                    $stmt->bindValue(':fecha', $fecha);
                    $stmt->bindValue(':hora', $hora);
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $stmt->execute();

                    echo json_encode(array('codigo' => 1));
                } else {
                    echo json_encode(array('codigo' => 2));
                }
            } catch (Exception $e) {
                echo json_encode($e->getMessage());
            } finally {
                $dbh = null;
            }
        } else {
            echo json_encode(array('codigo' => 0));
        }
    }
}

if ($method == 'delete') {
    $mail = $params->falta->email;
    $fecha = $params->falta->fecha;

    if ($params->falta->hora == 'Todo el día') {
        $hora = '00:00';
    } else {
        $hora = $params->falta->hora;
    }

    $fecha = new DateTime($fecha);

    try {
        $dbh = ConexionDB::conectar();

        $stmt = $dbh->prepare("select * from docentes where email = :mail");
        $stmt->bindValue(':mail', $mail);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $docente = $stmt->fetch();



        $stmt = $dbh->prepare("delete from docente_ausente where (dni_docente = :dni) and (fecha = :fecha) and (hora = :hora)");
        $stmt->bindValue(':dni', $docente['dni']);
        $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
        $stmt->bindValue(':hora', $hora);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        echo json_encode(array('codigo' => 1));
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    } finally {
        $dbh = null;
    }
}

if ($method == 'update') {

    $falta = $params->falta;
    $newFecha = $params->newFecha;
    $newHora = $params->newHora;

    $date = new DateTime($falta->fecha);
    $auxDate = new DateTime('now');
    $correctDay = compararFechas($newFecha, $auxDate->format('Y-m-d'));

    if ($falta->hora == 'Todo el día') {
        $hora = '00:00';
    } else {
        $hora = $falta->hora;
    }

    if ($newHora == '00:00') {
        if ($correctDay > 0 || ($correctDay == 0 && obtenerHoraGuardia() == '8:15')) {
            try {
                $dbh = ConexionDB::conectar();

                $stmt = $dbh->prepare("update docente_ausente set fecha = :newfecha, hora = :newhora where dni_docente = :dni and fecha = :fecha and hora = :hora");
                $stmt->bindValue(':newfecha', $newFecha);
                $stmt->bindValue(':newhora', $newHora);
                $stmt->bindValue(':dni', $falta->user->dni);
                $stmt->bindValue(':fecha', $date->format('Y-m-d'));
                $stmt->bindValue(':hora', $hora);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                $f = array(
                    'email' => $falta->user->email,
                    'fecha' => $newFecha,
                    'hora' => $newHora,
                    'user' => $falta->user
                );

                echo json_encode(array('error' => 0));
            } catch (Exception $e) {
                echo json_encode($e->getMessage());
            } finally {
                $dbh = null;
            }
        } else {
            echo json_encode(array('error' => 1));
        }
    } else {
        if ($correctDay >= 0 && comprobarHora($newHora)) {
            try {
                $dbh = ConexionDB::conectar();

                $stmt = $dbh->prepare("update docente_ausente set fecha = :newfecha, hora = :newhora where dni_docente = :dni and fecha = :fecha and hora = :hora");
                $stmt->bindValue(':newfecha', $newFecha);
                $stmt->bindValue(':newhora', $newHora);
                $stmt->bindValue(':dni', $falta->user->dni);
                $stmt->bindValue(':fecha', $date->format('Y-m-d'));
                $stmt->bindValue(':hora', $hora);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();

                $falta = array(
                    'email' => $falta->user->email,
                    'fecha' => $newFecha,
                    'hora' => $newHora,
                    'user' => $falta->user
                );

                echo json_encode(array('error' => 1));
            } catch (Exception $e) {
                echo json_encode($e->getMessage());
            } finally {
                $dbu = null;
            }
        } else {
            echo json_encode(array('error' => 0));
        }
    }
}


function compararFechas($primera, $segunda)
{
    $valoresPrimera = explode("-", $primera);
    $valoresSegunda = explode("-", $segunda);

    $diaPrimera    = $valoresPrimera[2];
    $mesPrimera  = $valoresPrimera[1];
    $anyoPrimera   = $valoresPrimera[0];

    $diaSegunda   = $valoresSegunda[2];
    $mesSegunda = $valoresSegunda[1];
    $anyoSegunda  = $valoresSegunda[0];

    $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
    $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

    if (!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)) {
        return 0;
    } elseif (!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)) {
        return 0;
    } else {
        return  $diasPrimeraJuliano - $diasSegundaJuliano;
    }
}

function comprobarHora($horaUpdate)
{
    $horas = array(
        '8:15' => 1,
        '9:10' => 2,
        '10:05' => 3,
        '11:00' => 4,
        '11:30' => 5,
        '12:25' => 6,
        '13:20' => 7,
        '14:15' => 8,
        '15:10' => 9
    );
    $hora = obtenerHoraGuardia();
    if ($hora != 'out') {
        if ($horas[$horaUpdate] <= $horas[$hora]) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
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
        return 'out';
    }
}
