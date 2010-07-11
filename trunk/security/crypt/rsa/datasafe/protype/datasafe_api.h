/*
datasafe_api.h
Written by Ken Xu (Kyle<xuqinyong@gmail.com>)
Copyright (C) 2010, ShopEx. 
All rights reserved.
*/
#ifndef _DATASAFE_API_
#define _DATASAFE_API_

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

DLLENTRY void shopex_data_encrypt(
	char *input,
	char *output
);

DLLENTRY void shopex_data_decrypt(
	char *input,
	char *output
);


#ifdef  __cplusplus
}
#endif

#endif