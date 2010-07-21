#! /usr/bin/env python
import ctypes

datasafe = ctypes.CDLL('/usr/lib/libdatasafe.so')
is_encrypted = datasafe.is_encrypted
print is_encrypted("/etc/shopex/skomart.com/setting.conf.en")
print is_encrypted("/etc/shopex/skomart.com/setting.conf")

#shopex_read_conf_file =  datasafe.shopex_read_conf_file
#return_string = pointer(c_char_p())
#return_len = c_int(0)
#shopex_read_conf_file("/etc/shopex/skomart.com/setting.conf.en",ctypes.byref(return_string),ctypes.byref(return_len))
#return_string = (c_char_p * 2)（）
#return_len = c_int(0)
#shopex_read_conf_file("/etc/shopex/skomart.com/setting.conf.en",pointer(return_string),pointer(return_len))

shopex_read_pubkeypos_in_file = datasafe.shopex_read_pubkeypos_in_file
return_string = pointer(c_char_p())
shopex_read_conf_file("/etc/shopex/skomart.com/setting.conf",ctypes.byref(return_string))
print return_string