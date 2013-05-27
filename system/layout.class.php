<?php
class Sys_Layout {
    private $view;
    private $viewpath;
    
    function __construct($view) {
        if (file_exists(VIEWS.$view.'.html')) {
            $this->view = $view;
            $this->viewpath = VIEWS.$view.'.html';
            return true;
        }
        else {
            return false;
        }
    }
    
    function render($data, $output = true) {
        $html = file_get_contents($this->viewpath);
        foreach($data as $k=>$v) {
            $html = str_replace('{'.$k.'}', $v, $html);
        }
        if ($output == true) {
            echo $html;
        }
        else {
            return $html;
        }
    }
    
    function renderPart($view, $data) {
        if (file_exists(VIEWS.$view.'.html')) {
            $html = file_get_contents(VIEWS.$view.'.html');
            foreach($data as $k=>$v) {
                $html = str_replace('{'.$k.'}', $v, $html);
            }
            return $html;
        }
        else {
            return false;
        }
    }
}