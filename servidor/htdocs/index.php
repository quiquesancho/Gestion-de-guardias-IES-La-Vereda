<?php
require_once '../bootstrap.php';

$uri = basename($_SERVER['REQUEST_URI']);

if ($uri == '') {
    echo 'Servidor';
} elseif (substr($uri, 0, 5) == "login") {
    require '../src/LDAPConn.php';
    require '../src/login.php';
} elseif ($uri == "subirXML") {
    require '../src/subirxml.php';
} elseif ($uri == "faltas") {
    require '../src/faltas.php';
} elseif ($uri == "users") {
    require '../src/users.php';
} elseif ($uri == "docentes") {
    require '../src/docentes.php';
} elseif ($uri == "guardias") {
    require '../src/guardias.php';
} else {
    header('Status:404 Not Found');
    echo '<html><body>Página No Encontrada</body></html>';
}
