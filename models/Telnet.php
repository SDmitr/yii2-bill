<?php
namespace app\models;

class Telnet
{
    private $host;
    private $port;
    private $login;
    private $password;
    private $length;
    private $socket;
    
    public function __construct($host, $login, $password, $port, $length)
    {
        $this->host = $host;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
        $this->length = $length;
    }
    
    public function connect()
    {
        $string = '';
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($this->socket, $this->host, $this->port);
        socket_write($this->socket, $this->login . "\n");
        socket_write($this->socket, $this->password . "\n");
        socket_write($this->socket, "enable\n");
        $this->read();
        socket_write($this->socket, "config\n");
        $this->read();
        socket_write($this->socket, "terminal length 0\n");
        $this->read();
        socket_write($this->socket, "terminal width 0\n");
        $this->read();
        socket_write($this->socket, "exit\n");
        $this->read();
        
        return true;
    }
    
    public function getPowerOlt($interface)
    {
        $cmd = "show epon optical-transceiver-diagnosis interface " . $interface . "\n";
        $string = '';
        socket_write($this->socket, $cmd);
        $power = $this->read();
        if (!empty($power[3])) {
            $power = preg_split("/\s+/", $power[3]);
            $result = (float) $power[1];
        } else {
            $result = 0;
        }
        return $result;
    }
    
    public function getDiagOnu($interface)
    {
        $cmd = "show epon interface " . $interface . " onu ctc optical-transceiver-diagnosis\n";
        $string = '';
        socket_write($this->socket, $cmd);
        
        $result = array();
        $info = $this->read();
        
        if (!empty($info[1])) {
            $temperature = explode(":", $info[1]);
            if (!empty($temperature[1])) {
                $onu_temperature = (float) trim($temperature[1]);
                $result['temperature'] = is_infinite($onu_temperature) ? null : $onu_temperature ;
            } else {
                $result['temperature'] = null;
            }
        } else {
            $result['temperature'] = null;
        }
        
        if (!empty($info[4])) {
            $transmitted_power = explode(":", $info[4]);
            $onu_transmitted_power = (float) trim($transmitted_power[1]);
            $result['transmitted_power'] = is_infinite($onu_transmitted_power) ? null : $onu_transmitted_power;
        } else {
            $result['transmitted_power'] = null;
        }
  
        if (!empty($info[5])) {
            $power = explode(":", $info[5]);
            $onu_power = (float) trim($power[1]);
            $result['power'] = is_infinite($onu_power) ? null : $onu_power;
        } else {
            $result['power'] = null;
        }
        return $result;
    }
    
    public function getInterfaces()
    {
        $cmd = "show interface brief\n";
        $string = '';
        socket_write($this->socket, $cmd);
        return $this->read();
    }
    
    public function getOnu($interface)
    {
        $cmd = "show running-config interface ePON0/". $interface . "\n";
        $string = '';
        socket_write($this->socket, $cmd);
        return $this->read();
    }
    
    public function getStatus($interface, $status)
    {
        if ($status == 'active') {
            $cmd = "show epon active-onu interface ePON0/". $interface . "\n";
        } elseif ($status == 'inactive') {
            $cmd = "show epon inactive-onu interface ePON0/". $interface . "\n";
        } else {
            return false;
        }
        $string = '';
        socket_write($this->socket, $cmd);
        return $this->read();
    }
    
    public function setRebootOnu($interface)
    {
        $cmd = "epon reboot onu interface ". $interface . "\n";
        socket_write($this->socket, $cmd);
        socket_write($this->socket, "y\n");
        return true;
    }
    
    public function deleteOnu($interface)
    {
        $epon = explode(':', $interface);
        socket_write($this->socket, "config\n");
        socket_write($this->socket, "interface " . $epon[0] . "\n");
        $cmd = "no epon bind-onu sequence ". $epon[1] . "\n";
        socket_write($this->socket, $cmd);
        socket_write($this->socket, "exit\n");
        socket_write($this->socket, "exit\n");
        socket_write($this->socket, "write\n");
        return true;
    }
    
    
    public function getMac($mac)
    {
        $result = $mac;
        $result = str_replace('.', '', $result);
        $result = preg_replace('/(.{2})/', '\1:', $result);
        $result = substr($result, 0, -1);
        return $result;
    }
    
    public function getArp($ip)
    {
        $cmd = "show arp " . $ip . "\n";
        socket_write($this->socket, $cmd);
        return $this->read();
    }
    
    public function setPing($ip)
    {
        $cmd = "ping -n 1 -w 1 ". $ip . "\n";
        $string = '';
        socket_write($this->socket, $cmd);
        return $this->read();
    }

    public function read()
    {
        $string = '';
        while ($out = socket_read($this->socket, $this->length)) {
            $string = $string . $out;
            if (preg_match('/#/', $out)) {
                break;
            }
        }
        $result = explode("\n", $string);
        return $result;
    }

    public function close()
    {
        socket_close($this->socket);
        return true;
    }
}
