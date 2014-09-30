<?php
namespace Modules\Main\Services;
if (!defined('SYSTEM')) exit('No direct script access allowed');

class Test extends \System\Baseservice {
    function someFunction() {
        return 'Hi :D';
    }
}