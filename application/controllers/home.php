<?php
namespace Application\Controller;

class Home extends \System\Basecontroller {
    function index($arg = null) {
        $layout = new \System\Layout('home');
        
        $test = $this->services->test;
        $content = $test->someFunction();
        
        $layout->render(array('content'=>$content, 'title'=>'Home'));
    }
}