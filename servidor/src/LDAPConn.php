<?php

class LDAP
{
    private $connectionId;

    public function __construct()
    {
        $this->connectionId = ldap_connect($_ENV['LDAP_URI'], $_ENV['LDAP_PORT']);
        ldap_set_option($this->connectionId, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    public function login($user, $pass)
    {
        return @ldap_bind($this->connectionId, 'uid=' . $user . $_ENV['LDAP_USERS'], $pass);
    }

    public function getMail($user)
    {
        $filter = array();
        $uid = ldap_search($this->connectionId, 'uid='.$user.$_ENV['LDAP_USERS'], '(mail=*)');
        $info = ldap_get_entries($this->connectionId, $uid);

        for($i = 0; $i < $info[0]['uid']['count']; $i++){
            array_push($filter, $info[0]['mail'][$i]);
        }

        return $filter;
    }

    public function getRole($user)
    {
        if($this->isDocente($user)){
            return 'docente';
        }
        if($this->isSecre($user)){
            return 'secretaria';
        }
        if($this->isAdmin($user)){
            return 'admin';
        }
    }

    private function isDocente($user)
    {
        $filter = array();
        $miembros = ldap_search($this->connectionId, 'cn=Docente'.$_ENV['LDAP_GROUP'], '(memberUid=*)');
        $info = ldap_get_entries($this->connectionId, $miembros);

        for($i = 0; $i < $info[0]['memberuid']['count']; $i++){
            array_push($filter, $info[0]['memberuid'][$i]);
        }

        return in_array($user, $filter);
    }

    private function isSecre($user)
    {
        $filter = array();
        $miembros = ldap_search($this->connectionId, 'cn=Secretaria'.$_ENV['LDAP_GROUP'], '(memberUid=*)');
        $info = ldap_get_entries($this->connectionId, $miembros);

        for($i = 0; $i < $info[0]['memberuid']['count']; $i++){
            array_push($filter, $info[0]['memberuid'][$i]);
        }

        return in_array($user, $filter);
    }

    private function isAdmin($user)
    {
        $filter = array();
        $miembros = ldap_search($this->connectionId, 'cn=Admin'.$_ENV['LDAP_GROUP'], '(memberUid=*)');
        $info = ldap_get_entries($this->connectionId, $miembros);

        for($i = 0; $i < $info[0]['memberuid']['count']; $i++){
            array_push($filter, $info[0]['memberuid'][$i]);
        }

        return in_array($user, $filter);
    }
}