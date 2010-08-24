<?php exit(); ?>a:2:{s:5:"value";s:1159:"{
  "elements":[
    {
      "type": "area",
	  "fill-alpha": 0.1,
      "colour":    "#3367ac", 
      "font-size": 12,
      "width":     2,
      "dot-size":  4,
      
      "text":      "<?php echo ((isset($this->_vars['title']) && ''!==$this->_vars['title'])?$this->_vars['title']:'null'); ?>",   //data
      
      "values" :   <?php echo ((isset($this->_vars['values']) && ''!==$this->_vars['values'])?$this->_vars['values']:'null'); ?>
        
    }
  ],

  "y_axis":{
    "colour":"#d4e0f1",
    "grid-colour":  "#e3e8ec",
    "labels":{"colour":"#333333"},
    
    "max": <?php echo ((isset($this->_vars['max']) && ''!==$this->_vars['max'])?$this->_vars['max']:'null'); ?>,  //data
    "steps" :<?php echo ((isset($this->_vars['steps']) && ''!==$this->_vars['steps'])?$this->_vars['steps']:'null'); ?>  //data
  },
  "x_axis":{
   "colour":"#d4e0f1",
   "grid-colour":  "#e3e8ec",
   "labels":{
     "colour":"#333333",
     "size":12,
     
     "labels": <?php echo ((isset($this->_vars['labels']) && ''!==$this->_vars['labels'])?$this->_vars['labels']:'null'); ?>
   }
  },
  
  "bg_colour": "#ffffff"

}";s:6:"expire";i:0;}