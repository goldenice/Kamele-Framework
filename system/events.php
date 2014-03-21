<?php
namespace System;

class Events extends \System\Singleton {
    private $listeners;
    
    function __construct() {
        parent::__construct();
        
        $this->loadModuleListeners();
    }
    
    private function loadModuleListeners() {
        // Define dir- and filenames
        $moduledir = 'modules';
        $listenerfile = 'listeners.json';
        
        // Open the directory
        $fh = opendir($moduledir);
    
        // Read listeners from all the files inside the module directories
        if ($fh == false) {
            return false;
        }
        else {
            while ($item = readdir($fh)) {
                $item = $moduledir.'/'.$item;
                if (!is_dir($item)) {
                    continue;
                }
                if (file_exists($item.'/'.$listenerfile)) {
                    $listeners = json_decode(file_get_contents($item.'/'.$listenerfile), true);
                    foreach ($listeners as $k=>$v) {
                        if (isset($v['event']) and isset($v['listener'])) {
                            $this->addListener($v['event'], $v['listener']);
                        }
                    }
                }
            }
        }
    }
    
    function fireEvent($name, &$data = null) {
        if (!empty($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $k=>$v) {
                if ($data !== null) {
                    call_user_func_array($v, array(&$data));
                }
                else { 
                    call_user_func($v);
                }
            }
        }
        return $data;
    }
    
    function addListener($event, $function) {
        // Make sure the event does not exist in the array of listeners
        $this->deleteListener($event, $function);
        
        // Then (re-)add the listener to the array
        $this->listeners[$event][] = $function;
        
        return true;
    }
    
    function deleteListener($event, $function) {
        if (!empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $k=>$v) {
                if ($v == $function) {
                    unset($this->listeners[$event][$k]);
                    return true;
                }
            }
        }
        return false;
    }
}