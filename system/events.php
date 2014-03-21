<?php
namespace System;

class Events extends \System\Singleton {
    private $listeners;
    static public $priorities = array('LOWEST'=>10, 'LOW'=>25, 'NORMAL'=>50, 'HIGH'=>75, 'HIGHEST'=>90, 'MONITOR'=>100);        // The numbers are defined as a form of percentages
    
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
                            if (isset($v['priority'])) {
                                $this->addListener($v['event'], $v['listener'], $v['priority']);
                            }
                            else {
                                $this->addListener($v['event'], $v['listener']);
                            }
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
                    call_user_func_array($v->func, array(&$data));
                }
                else { 
                    call_user_func($v->func);
                }
            }
        }
        return $data;
    }
    
    function prioStringToInt($priority) {
        return self::$priorities[strtoupper($priority)];
    }
    
    function addListener($event, $function, $priority = 'NORMAL') {
        $priority = $this->prioStringToInt($priority);
        
        // Make sure the event does not exist in the array of listeners
        $this->deleteListener($event, $function);
        
        // Then (re-)add the listener to the array
        $newevent = new \StdClass;
        $newevent->func = $function;
        $newevent->prio = $priority;
        
        if (!empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $k=>$v) {
                if ($k == 0 and $v->prio > $priority) {
                    array_splice($this->listeners[$event], $k, 0, array($newevent));
                    break;
                }
                if (empty($this->listeners[$event][$k+1]) or $this->listeners[$event][$k+1]->prio > $priority) {
                    array_splice($this->listeners[$event], $k+1, 0, array($newevent));
                    break;
                }
            }
        }
        else {
            $this->listeners[$event][] = $newevent;
        }

        return true;
    }
    
    // This deletes all listeners with given function, no matter what priority!
    function deleteListener($event, $function) {
        if (!empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $k=>$v) {
                if ($v->func == $function) {
                    unset($this->listeners[$event][$k]);
                    return true;
                }
            }
        }
        return false;
    }
}