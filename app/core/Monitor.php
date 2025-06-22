<?php

namespace app\core;

class Monitor {
    public static function checkServiceIsActive(string $service): bool{

        $output = trim(shell_exec("sudo systemctl is-active $service"));
        
        return $output === 'active'; 
    }

}
