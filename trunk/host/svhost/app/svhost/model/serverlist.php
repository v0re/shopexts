<?php

class svhost_mdl_serverlist extends dbeav_model{
    var $has_many = array(
        'http'=>'http:replace',
        'database'=>'database:replace',
        'ftp'=>'ftp:replace',
    );
}