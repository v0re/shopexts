#! /usr/bin/env python
import ctypes

datasafe = ctypes.CDLL('/usr/lib/libdatasafe.so')
is_encrypted = datasafe.is_encrypted
print is_encrypted("/etc/shopex/skomart.com/setting.conf.en")
print is_encrypted("/etc/shopex/skomart.com/setting.conf")

shopex_read_conf_file =  datasafe.shopex_read_conf_file
return_string = "00";
return_len = 0;
shopex_read_conf_file("/etc/shopex/skomart.com/setting.conf.en",ctypes.byref(return_string),ctypes.byref(return_len))
