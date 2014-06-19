<?php
namespace System;

/**
 * Layout class, basic templating system
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.0-alpha
 */
class Layout {
    /**
     * @access  private
     * @var     string      The view-file as specified by the controller
     */
    private $view;
    
    /**
     * @access  private
     * @var     string      The path to the main view-file
     */
    private $viewpath;
    
    /**
     * Constructor, loads main view
     * 
     * @access  public
     * @param   string      $view       The view-file to load (without the .html extension)
     * @return  boolean
     */
    public function __construct($view) {
        if (file_exists($view.'.html')) {
            $this->view = $view;
            $this->viewpath = $view.'.html';
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Render function, renders the main view substituting placeholders with additional code
     * 
     * @access  public
     * @param   array       $data       The placeholder to substitute, with an key=>value structure
     * @param   boolean     $output     Should we write this to output-buffer, or return the HTML?
     * @return  string|null
     */
    public function render($data, $output = true) {
        $data['baseurl'] = BASEURL;
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
    
    /**
     * Renders a separate view
     * 
     * @access  public
     * @param   string      $view       Path to the view-file, without .html
     * @param   array       $data       Associative array with substitution data
     * @return  string|boolean
     */
    function renderPart($view, $data) {
        if (file_exists($view.'.html')) {
            $data['baseurl'] = BASEURL;
            $html = file_get_contents($view.'.html');
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