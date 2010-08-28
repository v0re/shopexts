<?php

class svhost_mdl_serverlist extends dbeav_model{
    var $has_one = array(
        'http'=>'http:replace',
        'database'=>'database:replace',
        'ftp'=>'ftp:replace',
    );
}