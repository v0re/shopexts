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

DLLENTRY void shopex_conf_rsa_encrypt(
	char *input,
	int input_len,
	char **output,
	int *output_len
);

DLLENTRY void shopex_conf_rsa_decrypt(
	char *input,
	int input_len,
	char **output,
	int *output_len
);

DLLENTRY void shopex_data_rsa_encrypt(
    char *keyfile_path,
	char *input,
	int input_len,
	char **output,
	int *output_len
);

DLLENTRY void shopex_data_rsa_decrypt(
    char *keyfile_path,
	char *input,
	int input_len,
	char **output,
	int *output_len
);

DLLENTRY void test_get_shopex_key(

);


DLLENTRY void test_get_user_key(

);

DLLENTRY void test_shopex_data_rsa_encrypt(

);

#ifdef  __cplusplus
}
#endif

#endif