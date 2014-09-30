<?php
namespace Modules\Main\Models;
if (!defined('SYSTEM')) exit('No direct script access allowed');

class Example extends \System\Basemodel {
    function exampleMethod() {
        // These functions should be pretty much CRED for the database
        return 'Example data';
    }
}