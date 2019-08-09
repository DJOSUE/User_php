<?php
/*
 * Description of settings
 *
 * @author dhuaraca
 */
class settings {
    
    
    /**
    * Stores the object of the mysql class
    * @var object 
    */
    var $db;    
    
    function __construct() {
        
    }
    
    function getDomain() {            
        return $this->db->getRow("SELECT `domain` FROM `".MLS_PREFIX."settings`"); 
    }
    
    function getDomain() {            
        return $this->db->getRow("SELECT `domain` FROM `".MLS_PREFIX."settings`"); 
    }
    
}
