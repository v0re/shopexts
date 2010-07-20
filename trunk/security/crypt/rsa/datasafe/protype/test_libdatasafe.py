#! /usr/bin/env python
import ctypes

datasafe = ctypes.CDLL('/usr/lib/libdatasafe.so')
is_encrypted = datasafe.is_encrypted
print is_encrypted("/etc/shopex/skomart.com/setting.conf.en");
print is_encrypted("/etc/shopex/skomart.com/setting.conf");
