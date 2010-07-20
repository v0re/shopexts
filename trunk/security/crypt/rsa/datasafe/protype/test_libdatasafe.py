#! /usr/bin/env python
import ctypes


lib_handle = ctypes.CDLL('/usr/lib/libdatasafe.so')


print lib_handle