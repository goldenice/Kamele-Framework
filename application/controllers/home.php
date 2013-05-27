<?php
class Controller_Home {
    function index($arg = null) {
        $layout = new Sys_Layout('home');
        $layout->render(array('content'=>'Some awesome controller content', 'title'=>'Home'));
    }
}