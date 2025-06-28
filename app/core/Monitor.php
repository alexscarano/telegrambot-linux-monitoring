<?php

namespace app\core;

class Monitor {
    public static function checkServiceIsActive(string $services): bool{
        
        $servicesArr = explode(' ', $services);
        foreach ($servicesArr as $service) {
            $output = trim(shell_exec("sudo systemctl is-active $service"));
            if ($output !== 'active') {
                return false;
            }
        }
        return true;

    } 

}
