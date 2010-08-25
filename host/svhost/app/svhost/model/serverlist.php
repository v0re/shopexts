<?php

class svhost_mdl_serverlist extends dbeav_model{
    var $has_many = array(
        'http/setting'=>'http:append',
        'database'=>'database:append',
        'ftp'=>'ftp:append',
    );
}