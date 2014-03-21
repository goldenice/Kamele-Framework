<?php
namespace Modules\Main\Controllers;

class Home extends \System\Basecontroller {
    function index($arg = null) {		
        $layout = new \System\Layout('modules/main/views/home');

        $test = $this->loader['\Modules\Main\Models\Example'];
        $content = $test->exampleMethod();
        
        $layout->render(array('content'=>$content, 'title'=>'Home'));
        
        
        $this->events->fireEvent('pageDone');
        $this->events->debug();
    }
    
    static function lowPrio(&$data = null) {
        echo 'Low priority';
    }
    
    static function normalPrio(&$data = null) {
        echo 'Normal priority';
    }
    
    static function highPrio(&$data = null) {
        echo 'High priority';
    }
    
    static function systemStart() {
        echo 'This is an event that runs because of static listeners. Awesome stuff.';
    }
}