/*
   infosec_api.h
   
   The header file of ICBC demanded API writen in ANSI C
   Written by Yang Hongxuan (yanghx <yanghx@infosec.com.cn>)
  
   Copyright (C) 2002, Infosec Century, Inc. Created 2002. 
   All rights reserved.
*/

#ifndef _INFOSEC_API_
#define _INFOSEC_API_

#if defined (_WIN32)
 #ifndef _DLLEXPORT_
   #define DLLENTRY	__declspec(dllimport)
 #else
   #define DLLENTRY	 __declspec(dllexport)
 #endif
#else
 #define	DLLENTRY
#endif

#ifdef          __cplusplus
extern "C" {
#endif

DLLENTRY extern int CEA_encrypt(
unsigned char * in, unsigned char * out, unsigned long in_len,char * key, int key_len);

#ifndef  _RETURN_VALUE_
#define  _RETURN_VALUE_
typedef enum RETURN_VALUE
{
	RET_SUCCESS = 0,
	ERR_BAD_PARAMETERS = -1,
	ERR_MALLOC = -2,
	ERR_PUBLICKEY_ENCODE = -3,
	ERR_PUBLICKEY_DECODE = -4,
	ERR_PRIVATEKEY_ENCODE = -5,
	ERR_PRIVATEKEY_DECODE = -6,
	ERR_PUBLICKEY_ENCRYPT = -7,
	ERR_PUBLICKEY_DECRYPT = -8,
	ERR_PRIVATEKEY_ENCRYPT = -9,
	ERR_PRIVATEKEY_DECRYPT = -10,
	ERR_CEA_ENCRYPT = -11,
	ERR_CEA_DECRYPT = -12,
	ERR_GET_CERT_INFO = -13,
	ERR_GEN_KEY = -14,
	ERR_MPI_CONVERT = -15,
	ERR_FILE = -16,
	ERR_MD	= -17,
	ERR_OTHER = -18
} RETURN_VALUE;
#endif

/*
Description:
  sign a string

parameter:
      src           (in)--data to be sign
      srcLen        (in)--src 's length
      privateKey    (in)--private key
      keyLen        (in)--private key len
      keyPass       (in)--private key password,must be '\0' end string
      signBuf       (out)-signature data buffer
      signBufLen    (out)-signature buffer' length

return:
      0  for successful
      -1 src error
      -2 private key error  
      -3 decrypt error
remark:
     the caller has obligation to free the  'signBuf'
*/
DLLENTRY int sign
( 
  unsigned char *src,
  int srcLen,
  unsigned char *privateKey,
  int keyLen,
  char *keyPass,
  unsigned char **signBuf,
  int *signBufLen
);
/*
Description:
  verify a signature

parameter:
      src           (in)--data to be verify
      srcLen        (in)--src 's length
      cert          (in)--certificate
      certLen       (in)--certificate len
      signBuf       (in)--signature buffer
      signBufLen    (in)--signature buffer' length

return:
      0  signature is good
      -1 decode cert failure
*/
DLLENTRY int verifySign
(
   unsigned char *src,
   int srcLen,
   unsigned char *cert,
   int certLen,
   unsigned char *signBuf,
   int signBufLen
);
/*
Description:
  base64 encode

parameter:
      src           (in)--src to be encoded
      srcLen        (in)--src len
      dst           (out)-base64 encode result
      dstLen        (out)-dst len
return:
      0  get ok
      -1 failure
remark:
     caller must free "dst"
*/
DLLENTRY int base64enc(unsigned char *src,int srcLen,unsigned char **dst,int *dstLen);

/*
Description:
  base64 decode

parameter:
      src           (in)--src to be decode
      srcLen        (in)--src len
      dst           (out)-base64 decode result
      dstLen        (out)-dst len
return:
      0  get ok
      -1 failure
remark:
     caller must free "dst"
*/
DLLENTRY int base64dec(unsigned char *src,int srcLen,unsigned char **dst,int *dstLen);
/*
Description:
  get certificate ID

parameter:
      mertCert           (in)--certificate
      mertCertLen        (in)--cert len
      id                 (out)-id
      idLen              (out)-id len
return:
      0  get ok
      -1 failure
remark:
     caller must free "id"
*/
DLLENTRY int getCertID(unsigned char *mertCert,int mertCertLen,char **id,int *idLen);

DLLENTRY void infosec_free(void *dst);

/*******************************************************/
/*                  part 1 : RSA的操作                 */
/*******************************************************/

/*1。1  RSA密钥对本身的操作 */

/* 说明：产生公钥和没有口令保护的私钥 */
/* 返回值：成功 0  失败 非0 */
/* 注意：存放公钥和私钥的缓冲区由接口分配，调用者来释放
         inKeyLen <= 4096 bits, 最好inKeyLen被32整除
*/
DLLENTRY int genRSAKey
(
   int inKeyLen,              /*所需RSA模数n的比特数，输入*/
   unsigned char *seed,       /*随机数种子，输入*/
   unsigned char **pubKeyBuf, /*存放公钥的缓冲区，输出*/
   int  *publicKeyLen,        /*ASN.1编码的公钥的长度*/
   unsigned char **priKeyBuf, /*存放私钥的缓冲区，输出*/
   int *privateKeyLen         /*ASN.1编码的私钥的长度*/
);

/* 说明：产生公钥和有口令保护的私钥 
   返回值：成功 0  失败 非0 
   注意：存放公钥和私钥的缓冲区由接口分配，调用者来释放
         inKeyLen <= 4096 bits, 最好inKeyLen被32整除.
		 初始口令为12345678
*/
DLLENTRY int genRSAKeyPwd
(
   int inKeyLen,             /*所需RSA模数n的比特数，输入*/
   unsigned char *seed,      /*随机数种子，输入*/
   unsigned char **pubKeyBuf,/*保存公钥的缓冲区，输出*/
   int  *publicKeyLen,        /*ASN.1编码的公钥的长度*/
   unsigned char **priKeyBuf,/*保存私钥的缓冲区，输出*/
   int *privateKeyLen,        /*ASN.1编码的私钥的长度*/
   char **initialKeyPass     /*保护私钥的初始口令，输出*/
);

/* 说明：从证书中获得公钥 
   注意：pubKeyBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 
*/
DLLENTRY int getPublicKey 
(
   unsigned char *cert,      /*证书数据，输入*/
   int certLen,              /*证书数据的长度，输入*/
   unsigned char **publicKey,/*获得的公钥的缓冲区，输出*/
   int *publicKeyLen         /*获得公钥的长度，输出*/
);

/* 说明：从口令保护的私钥中获得私钥
   注意：plainPrivateKey缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 
*/
DLLENTRY int getPrivateKey
(
   unsigned char *privateKey, /*口令保护的私钥*/
   int  privateKeyLen,        /*口令保护的私钥的长度*/
   char *privatePwd,          /*私钥保护口令*/
   int   pwdLen,              /*私钥保护口令长度*/
   unsigned char **plainPrivateKey,/*保存解密后的asn.1格式的私钥缓冲区*/
   int *plainPrivateKeyLen         /*解密后的asn.1格式的私钥长度*/
);

/* 说明：解密口令保护的私钥得到私钥 */
/* 注意：plainPrivateKey缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0
*/ 
DLLENTRY int decPrivateKey
(
  unsigned char *privateKey, /*加密的asn.1格式私钥,输入*/
  int keyLen,                /*私钥的字节数,输入*/
  char *keyPass,             /*私钥的保护口令,输入*/
  unsigned char **plainPrivateKey, /*保存解密后的私钥的缓冲区,*/
  int *plainPrivateKeyLen    /*解密后的私钥的长度,*/
);

/* 说明：修改私钥的保护口令,修改了保护口令得私钥仍然存放在privateKey
   返回值：成功 0  失败 非0 
*/
DLLENTRY int changePrivateKeyPass
(
   unsigned char *privateKey, /*口令保护的私钥*/
   int  privateKeyLen,        /*私钥的长度*/
   char *oldPwd,              /*旧口令*/
   int  oldPwdLen,            /*旧口令的长度*/
   char *newPwd,              /*新口令*/
   int  newPwdLen             /*新口令的长度*/
);

/*1。2 用RSA的私钥进行的操作 */ 

/* 说明：用口令保护的RSA私钥来加密数据 */
/* 注意：encBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0
 */
DLLENTRY int privateEncryptPwd
(
  unsigned char *src,       /*要加密的数据*/
  int srcLen,               /*要加密的数据的字节数*/
  unsigned char *privateKey,/*私钥*/
  int keyLen,               /*私钥的字节数*/
  char *keyPass,            /*口令*/
  unsigned char **encBuf,   /*加密以后数据存放的缓冲区指针*/
  int *encBufLen            /*加密以后数据的长度*/
);

/* 说明：用口令保护的RSA私钥来解密数据 */
/* 注意：decBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int privateDecryptPwd
(
   unsigned char *src,       /*要解密的数据*/
   int srcLen,               /*要解密的数据的字节数*/
   unsigned char *privateKey,/*私钥*/
   int keyLen,               /*私钥的字节数*/
   char *pass,               /*口令*/
   unsigned char **decBuf,   /*解密以后的数据存放的缓冲区指针*/
   int *decBufLen            /*解密以后的数据的长度*/
);

/* 说明：用没有口令保护的RSA私钥来加密数据 */
/* 注意：encBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int privateEncrypt
(
  unsigned char *src,        /*要加密的数据*/
  int srcLen,                /*要加密的数据的字节数*/
  unsigned char *privateKey, /*私钥*/
  int keyLen,                /*私钥的字节数*/
  unsigned char **encBuf,    /*加密以后的数据存放的缓冲区指针*/
  int *encBufLen             /*加密以后的数据的长度*/
);

/* 说明：用没有口令保护的RSA私钥来解密数据 */
/* 注意：decBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int privateDecrypt
(
  unsigned char *src,        /*要解密的数据*/
  int srcLen,                /*要解密的数据的字节数*/
  unsigned char *privateKey, /*私钥*/
  int keyLen,                /*私钥的字节数*/
  unsigned char **decBuf,    /*解密以后的数据存放的缓冲区指针*/
  int *decBufLen             /*解密以后的数据的长度*/
); 

/*1。3 用RSA的公钥进行的操作 */

/* 说明：用RSA公钥来加密数据 */
/* 注意：encBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int publicEncrypt
(
  unsigned char *src,       /*要加密的数据*/
  int srcLen,               /*要加密的数据的字节数*/
  unsigned char *publicKey, /*公钥*/
  int keyLen,               /*公钥的字节数*/
  unsigned char **encBuf,   /*加密以后的数据存放的缓冲区指针*/
  int *encBufLen            /*加密以后的数据的长度*/
);

/* 说明：用RSA公钥来解密数据 */
/* 注意：decBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int publicDecrypt
(
  unsigned char *src,      /*要解密的数据*/
  int srcLen,              /*要解密的数据的字节数*/
  unsigned char *publicKey,/*公钥*/
  int keyLen,              /*公钥的字节数*/
  unsigned char **decBuf,  /*解密以后的数据存放的缓冲区指针*/
  int *decBufLen           /*解密以后的数据的长度*/
);

/* 说明：用证书中的RSA公钥来加密数据 */
/* 注意：encBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int publicEncryptCert
(
   unsigned char *src,    /*要加密的数据*/
   int srcLen,            /*要加密的数据的长度*/
   unsigned char *cert,   /*证书*/
   int certLen,           /*证书长度*/
   unsigned char **encBuf,/*加密后的数据缓冲区指针*/
   int *encBufLen         /*加密后的数据的长度*/
);

/* 说明：用证书中的RSA公钥来解密数据 */
/* 注意：decBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
/* 这个是为了使用者方便新加的接口*/
DLLENTRY int publicDecryptCert
(
   unsigned char *src,    /*要解密的数据*/
   int srcLen,            /*要解密的数据的长度*/
   unsigned char *cert,   /*证书*/
   int certLen,           /*证书长度*/
   unsigned char **encBuf,/*加密后的数据缓冲区指针*/
   int *encBufLen         /*加密后的数据的长度*/
);

/*********************************************************/
/*              part 2: Des密码操作                      */
/*********************************************************/

/* 说明：用DES加/解密接口 */
/* 返回值：成功 0  失败 非0 */
/* 注意：encodedBuf缓冲区由接口分配，调用者来释放
         明文长度不被8整除时，自动添加了若干个值为0x00的字节，
		 以满足明文长度被8整除的条件
*/
DLLENTRY int desEncDec
(
   unsigned char *src,    /*要加密的数据*/
   int  srcLen,           /*要加密的数据的长度*/
   unsigned char *key,    /*加/解密 密钥*/
   int  keyLen,           /*加/解密 密钥的长度*/
   int  flag,             /*1 for encryption, 0 for decryption*/
   unsigned char **encodedBuf,/*加密以后的数据的缓冲区指针*/
   int *encodedBufLen         /*加密以后的数据长度*/
);

/* 说明：产生一个用于tripple DES加/解密的密钥 */
/* 注意：keyBuf缓冲区由接口分配，调用者来释放
   返回值：成功 0  失败 非0 */
DLLENTRY int genDESKey
(
   int length,            /*输入要产生的密钥的长度 >= 24 */
   unsigned char *seed,   /*输入的随机数种子*/
   unsigned char **keyBuf,/*产生的密钥*/
   int *keyLen            /*产生的密钥的长度*/
);

#ifdef          __cplusplus
}
#endif

#endif
