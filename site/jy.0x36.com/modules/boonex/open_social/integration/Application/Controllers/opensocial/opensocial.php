<?php

/* BoonEx Opensocial integration*/

class opensocialController extends baseController {

  public function certificates($params) {
    if ($params[3] == 'xoauth_public_keyvalue') {
      readfile(PartuzaConfig::get('gadget_server') . '/public.crt');
    }
  }
}
