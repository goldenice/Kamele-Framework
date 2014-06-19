<?php
namespace System;

/**
 * Events class
 * Handles event firing and listeners
 * 
 * @package     Kamele Framework
 * @subpackage  System
 * @author      Rick Lubbers <me@ricklubbers.nl>
 * @since       1.0-beta
 */
class Events extends Singleton {
    /** 
     * @access  private
     * @var     array       Multi-dimensional array which contains all listeners
     */
    private $listeners;
    
    /**
     * @access  public
     * @var     string[]    The numbers are defined as a form of percentages
     */
    static public $priorities = array('LOWEST'=>10, 'LOW'=>25, 'NORMAL'=>50, 'HIGH'=>75, 'HIGHEST'=>90, 'MONITOR'=>100);        
    
    /**
     * Constructor function
     * 
     * @access  public
     * @return  void
     */
    public function __construct() {
        parent::__construct();
        
        $this->loadModuleListeners();
    }
    
    /**
     * Loads listeners.json for every module if that file exists
     *
     * @access  public
     * @return  void
     */
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
    
    /**
     * Fires an event, carrying a pointer to a piece of data
     *
     * @access  public
     * @param   string      $name   Name of the event
     * @param   ?           $data   Pointer to variable of data, optional
     * @return  ?
     */
    public function fireEvent($name, &$data = null) {
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
    
    /**
     * Converts priority-string to a valid integer
     *
     * @access  public
     * @param   string      $priority   Priority
     * @return  int | void
     */
    public function prioStringToInt($priority) {
        return self::$priorities[strtoupper($priority)];
    }
    
    /**
     * Adds a listener to the current array of listeners
     *
     * @access  public
     * @param   string      $event      Name of the event
     * @param   array       $function   Class and method to execute when event is fired
     * @param   string|int  $priority   The priority when there is more than one listener listening on this event
     * @return  boolean
     */
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
    
    /**
     * Deletes a listener for a given event and a given function
     * 
     * @access  public
     * @param   string      $event      The event the listeners listenes to
     * @param   array       $function   The function to delete as listener
     * @return  boolean
     */
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