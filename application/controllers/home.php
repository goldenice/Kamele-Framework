<?php
class Controller_Home {
    function index($arg = null) {
        $layout = new Sys_Layout('home');
        $sample = new Model_Sample;
        $content = $sample->sampleMethod();
        $layout->render(array('content'=>$content, 'title'=>'Home'));
    }
}