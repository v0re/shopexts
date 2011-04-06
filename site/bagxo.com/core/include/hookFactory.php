<?php
/**
 * hookFactory 
 * 
 * @package 
 * @version $Id: hookFactory.php 1867 2008-04-23 04:00:24Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com> 
 * @license Commercial
 */

class hookFactory{

    var $system;

    function hookFactory(&$system){
        $this->system = $system;
    }
}
