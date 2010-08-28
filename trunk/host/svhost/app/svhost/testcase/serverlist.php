<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class serverlist extends PHPUnit_Framework_TestCase{
    
    public function setUp() {
        #$this->model = kernel::service('svhost_server', array('content_path'=>'svhost_server'));
        $this->model= kernel::single('svhost_mdl_serverlist');
    }

    public function testInsert(){
        $sdf = array(
            'server'=>array(
                'name'=>'Theplanet.No.1',
                'ip'=>'75.125.222.26',
                'farm'=>'美国休士顿Theplanet机房',
            ),
            'http'=>array(

                'name'=>'nginx',
                'htdocs'=>'/var/www/html',
                'conf'=>'/svr/nginx/conf/nginx.conf',
                'user'=>'apache',
                'group'=>'apache',
                
            ),
            'database'=>array(
                'name'=>'mysql',
                'conf'=>'/etc/my.cnf',
                'datadir'=>'/opt/mysqldb',
                'user'=>'mysql',
                'group'=>'mysql',
                'host'=>'127.0.0.1',
                'port'=>'3306',
                'root'=>'root',
                'password'=>'123456',

            ),
            'ftp'=>array(

                'name'=>'proftpd',
                'conf'=>'/srv/proftpd/etc/proftpd.conf',
                'root'=>'/var/www/html',
                'user'=>'ftpd',
                'group'=>'ftpd',
                'db'=>array(
                    'name'=>'proftpd',
                    'host'=>'127.0.0.1',
                    'port'=>'3306',
                    'root'=>'root',
                    'password'=>'123456',
                ),
            ),

        );
        $this->model->save($sdf);
    }

}
