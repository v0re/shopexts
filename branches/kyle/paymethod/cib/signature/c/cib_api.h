/*
    cib_api.h

    The header file of CIB demanded API writen in ANSI C
    Written by Kyle Xu (Kyle<xuqinyong@gmail.com>)

    Copyright (C) 2009, ShopEx. 
    All rights reserved.
*/
#ifndef _CIB_API_
#define _CIB_API_

#if defined (_WIN32)
 #ifndef _DLLEXPORT_
   #define DLLENTRY __declspec(dllimport)
 #else
   #define DLLENTRY  __declspec(dllexport)
 #endif
#else
 #define DLLENTRY
#endif

#ifdef  __cplusplus
extern "C" {
#endif

DLLENTRY void ANSIX99(
    char *mac_key,
    char *buf, 
    int len, 
    char *mac
);

DLLENTRY void bcd_to_asc(
    unsigned char *ascii_buf, 
    unsigned char *bcd_buf,
    int conv_len, 
    unsigned char type
);

DLLENTRY void DES(
    char *key,
    char *s_text,
    char *d_text
);


#ifdef  __cplusplus
}
#endif

#endif
