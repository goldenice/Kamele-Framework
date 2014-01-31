<?php
namespace Application\Controller;

class Home extends \System\Basecontroller {
    function index($arg = null) {
        $layout = new \System\Layout('home');
        
        $test = new \Application\Service\Test;
        $content = $test->someFunction();
        
        $layout->render(array('content'=>$content, 'title'=>'Home'));
    }
}