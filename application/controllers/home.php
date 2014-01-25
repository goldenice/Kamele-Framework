<?php
class Controller_Home {
    function index($arg = null) {
        $layout = new Sys_Layout('home');
        
        $test = new Service_Test;
        $content = $test->someFunction();
        
        $layout->render(array('content'=>$content, 'title'=>'Home'));
    }
}