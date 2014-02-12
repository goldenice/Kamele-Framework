<?php
namespace Modules\Main\Controllers;

class Home extends \System\Basecontroller {
    function index($arg = null) {
        $layout = new \System\Layout('modules/main/views/home');
        
        //$this->events->addListener('pageDone', '\Application\Controller\Home::displayFooter');
        
        $test = $this->loader['\Modules\Main\Models\Example'];
        $content = $test->exampleMethod();
        
        $layout->render(array('content'=>$content, 'title'=>'Home'));
        
        
        //$this->events->fireEvent('pageDone');
    }
    
    static function displayFooter(&$data = null) {
        echo 'Cool footer here. Event works :D';
    }
}