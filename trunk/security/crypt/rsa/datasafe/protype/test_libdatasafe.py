#! /usr/bin/env python
import ctypes

datasafe = ctypes.CDLL('/usr/lib/libdatasafe.so')
is_encrypted = datasafe.is_encrypted
print is_encrypted("/etc/shopex/skomart.com/setting.conf.en")
print is_encrypted("/etc/shopex/skomart.com/setting.conf")

shopex_read_pubkeypos_in_file = datasafe.shopex_read_pubkeypos_in_file
return_string = ctypes.pointer(ctypes.c_char_p())
shopex_read_pubkeypos_in_file("/etc/shopex/skomart.com/setting.conf",ctypes.byref(return_string))
print return_string