/*************************Product owner info********************************
*
*     author               : BoonEx 
*     contact info         : support@boonex.com
*                          BoonEx Unity Forums http://boonex.com/unity/forums/
*                          BoonEx Unity Answers http://www.boonex.com/unity/answers/home 
*
/*************************Product info**************************************
*
*                          Opensocial integration
*                          ------------------------
*     version              : 1.0
*     compability          : Dolphin 7 and upper (7.0.0)
*     License type         : CC_BY
*
***************************************************************************/

============================== 1. (RE-)CONFIGURATION ==============================


================== 2. SHELL POSSIBILITY TO UPDATE YOUR SHINDIG===================

1. Will carefully with your modified

a) modules\boonex\open_social\shindig\php\index.php
b) modules\boonex\open_social\shindig\php\config\container.php

You can create backups of these files as example

in shell console, goto your current 'modules/boonex/open_social/' directory, and execute:

svn co http://svn.apache.org/repos/asf/incubator/shindig/tags/shindig-project-1.0-incubating/

svn export shindig-project-1.0-incubating shindig

After update will need to reconfigure again (merge with old backup versions as example):

a) modules\boonex\open_social\shindig\php\index.php
b) modules\boonex\open_social\shindig\php\config\container.php

(or just merge with your backup versions)

=========================================================================================