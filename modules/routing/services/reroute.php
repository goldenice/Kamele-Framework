<?php
namespace Modules\Routing\Services;

class Reroute extends \System\Baseservice {
    static $class = null;
    
    static function determineNewRoute(&$stuff) {
        $routes = json_decode(file_get_contents('modules/routing/routes.json'), true);
        foreach ($routes as $k=>$v) {
            if ($v['original']['controller'] == $stuff[0] and $v['original']['method'] == $stuff[1]) {
                $stuff[0] = $v['new']['controller'];
                $stuff[1] = $v['new']['method'];
                return true;
            }
        }
        return false;
    }
}