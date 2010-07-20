#! /usr/bin/env python
import ctypes


lib_handle = ctypes.CDLL('./libpython_call.so')
test = lib_handle.test
print test(5)

testA = lib_handle.testA
print testA(1, 3)

testB = lib_handle.testB
print testB('aaaaaaaaaaaaaaaaaaaaa')
testB.restype = ctypes.c_char_p
print testB('bbbbbbbbbbbbbbbbbbbbbbb')

class AA(ctypes.Structure):
    _fields_=[("a", ctypes.c_int),("b", ctypes.c_int)]

aa = AA()
aa.a = 1
aa.b = 8
testC = lib_handle.testC
print testC(ctypes.byref(aa))

testD = lib_handle.testD
print testD(ctypes.byref(aa)), aa.a, aa.b

class BB(ctypes.Structure):
    _fields_=[("a", ctypes.c_int),("pB", ctypes.c_char_p),("c", ctypes.c_int)]

bb = BB()
bb.a = 1
bb.pB = 'ssssssssssssssssssss'
bb.c = 2
testE = lib_handle.testE
testE.restype = ctypes.c_char_p
print testE(ctypes.byref(bb)), bb.a, bb.c

bb.pB = None
testF = lib_handle.testF
print testF(ctypes.byref(bb)), bb.a, bb.pB, bb.c

print lib_handle