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
/*                  part 1 : RSA�Ĳ���                 */
/*******************************************************/

/*1��1  RSA��Կ�Ա���Ĳ��� */

/* ˵����������Կ��û�п������˽Կ */
/* ����ֵ���ɹ� 0  ʧ�� ��0 */
/* ע�⣺��Ź�Կ��˽Կ�Ļ������ɽӿڷ��䣬���������ͷ�
         inKeyLen <= 4096 bits, ���inKeyLen��32����
*/
DLLENTRY int genRSAKey
(
   int inKeyLen,              /*����RSAģ��n�ı�����������*/
   unsigned char *seed,       /*��������ӣ�����*/
   unsigned char **pubKeyBuf, /*��Ź�Կ�Ļ����������*/
   int  *publicKeyLen,        /*ASN.1����Ĺ�Կ�ĳ���*/
   unsigned char **priKeyBuf, /*���˽Կ�Ļ����������*/
   int *privateKeyLen         /*ASN.1�����˽Կ�ĳ���*/
);

/* ˵����������Կ���п������˽Կ 
   ����ֵ���ɹ� 0  ʧ�� ��0 
   ע�⣺��Ź�Կ��˽Կ�Ļ������ɽӿڷ��䣬���������ͷ�
         inKeyLen <= 4096 bits, ���inKeyLen��32����.
		 ��ʼ����Ϊ12345678
*/
DLLENTRY int genRSAKeyPwd
(
   int inKeyLen,             /*����RSAģ��n�ı�����������*/
   unsigned char *seed,      /*��������ӣ�����*/
   unsigned char **pubKeyBuf,/*���湫Կ�Ļ����������*/
   int  *publicKeyLen,        /*ASN.1����Ĺ�Կ�ĳ���*/
   unsigned char **priKeyBuf,/*����˽Կ�Ļ����������*/
   int *privateKeyLen,        /*ASN.1�����˽Կ�ĳ���*/
   char **initialKeyPass     /*����˽Կ�ĳ�ʼ������*/
);

/* ˵������֤���л�ù�Կ 
   ע�⣺pubKeyBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 
*/
DLLENTRY int getPublicKey 
(
   unsigned char *cert,      /*֤�����ݣ�����*/
   int certLen,              /*֤�����ݵĳ��ȣ�����*/
   unsigned char **publicKey,/*��õĹ�Կ�Ļ����������*/
   int *publicKeyLen         /*��ù�Կ�ĳ��ȣ����*/
);

/* ˵�����ӿ������˽Կ�л��˽Կ
   ע�⣺plainPrivateKey�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 
*/
DLLENTRY int getPrivateKey
(
   unsigned char *privateKey, /*�������˽Կ*/
   int  privateKeyLen,        /*�������˽Կ�ĳ���*/
   char *privatePwd,          /*˽Կ��������*/
   int   pwdLen,              /*˽Կ���������*/
   unsigned char **plainPrivateKey,/*������ܺ��asn.1��ʽ��˽Կ������*/
   int *plainPrivateKeyLen         /*���ܺ��asn.1��ʽ��˽Կ����*/
);

/* ˵�������ܿ������˽Կ�õ�˽Կ */
/* ע�⣺plainPrivateKey�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0
*/ 
DLLENTRY int decPrivateKey
(
  unsigned char *privateKey, /*���ܵ�asn.1��ʽ˽Կ,����*/
  int keyLen,                /*˽Կ���ֽ���,����*/
  char *keyPass,             /*˽Կ�ı�������,����*/
  unsigned char **plainPrivateKey, /*������ܺ��˽Կ�Ļ�����,*/
  int *plainPrivateKeyLen    /*���ܺ��˽Կ�ĳ���,*/
);

/* ˵�����޸�˽Կ�ı�������,�޸��˱��������˽Կ��Ȼ�����privateKey
   ����ֵ���ɹ� 0  ʧ�� ��0 
*/
DLLENTRY int changePrivateKeyPass
(
   unsigned char *privateKey, /*�������˽Կ*/
   int  privateKeyLen,        /*˽Կ�ĳ���*/
   char *oldPwd,              /*�ɿ���*/
   int  oldPwdLen,            /*�ɿ���ĳ���*/
   char *newPwd,              /*�¿���*/
   int  newPwdLen             /*�¿���ĳ���*/
);

/*1��2 ��RSA��˽Կ���еĲ��� */ 

/* ˵�����ÿ������RSA˽Կ���������� */
/* ע�⣺encBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0
 */
DLLENTRY int privateEncryptPwd
(
  unsigned char *src,       /*Ҫ���ܵ�����*/
  int srcLen,               /*Ҫ���ܵ����ݵ��ֽ���*/
  unsigned char *privateKey,/*˽Կ*/
  int keyLen,               /*˽Կ���ֽ���*/
  char *keyPass,            /*����*/
  unsigned char **encBuf,   /*�����Ժ����ݴ�ŵĻ�����ָ��*/
  int *encBufLen            /*�����Ժ����ݵĳ���*/
);

/* ˵�����ÿ������RSA˽Կ���������� */
/* ע�⣺decBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int privateDecryptPwd
(
   unsigned char *src,       /*Ҫ���ܵ�����*/
   int srcLen,               /*Ҫ���ܵ����ݵ��ֽ���*/
   unsigned char *privateKey,/*˽Կ*/
   int keyLen,               /*˽Կ���ֽ���*/
   char *pass,               /*����*/
   unsigned char **decBuf,   /*�����Ժ�����ݴ�ŵĻ�����ָ��*/
   int *decBufLen            /*�����Ժ�����ݵĳ���*/
);

/* ˵������û�п������RSA˽Կ���������� */
/* ע�⣺encBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int privateEncrypt
(
  unsigned char *src,        /*Ҫ���ܵ�����*/
  int srcLen,                /*Ҫ���ܵ����ݵ��ֽ���*/
  unsigned char *privateKey, /*˽Կ*/
  int keyLen,                /*˽Կ���ֽ���*/
  unsigned char **encBuf,    /*�����Ժ�����ݴ�ŵĻ�����ָ��*/
  int *encBufLen             /*�����Ժ�����ݵĳ���*/
);

/* ˵������û�п������RSA˽Կ���������� */
/* ע�⣺decBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int privateDecrypt
(
  unsigned char *src,        /*Ҫ���ܵ�����*/
  int srcLen,                /*Ҫ���ܵ����ݵ��ֽ���*/
  unsigned char *privateKey, /*˽Կ*/
  int keyLen,                /*˽Կ���ֽ���*/
  unsigned char **decBuf,    /*�����Ժ�����ݴ�ŵĻ�����ָ��*/
  int *decBufLen             /*�����Ժ�����ݵĳ���*/
); 

/*1��3 ��RSA�Ĺ�Կ���еĲ��� */

/* ˵������RSA��Կ���������� */
/* ע�⣺encBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int publicEncrypt
(
  unsigned char *src,       /*Ҫ���ܵ�����*/
  int srcLen,               /*Ҫ���ܵ����ݵ��ֽ���*/
  unsigned char *publicKey, /*��Կ*/
  int keyLen,               /*��Կ���ֽ���*/
  unsigned char **encBuf,   /*�����Ժ�����ݴ�ŵĻ�����ָ��*/
  int *encBufLen            /*�����Ժ�����ݵĳ���*/
);

/* ˵������RSA��Կ���������� */
/* ע�⣺decBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int publicDecrypt
(
  unsigned char *src,      /*Ҫ���ܵ�����*/
  int srcLen,              /*Ҫ���ܵ����ݵ��ֽ���*/
  unsigned char *publicKey,/*��Կ*/
  int keyLen,              /*��Կ���ֽ���*/
  unsigned char **decBuf,  /*�����Ժ�����ݴ�ŵĻ�����ָ��*/
  int *decBufLen           /*�����Ժ�����ݵĳ���*/
);

/* ˵������֤���е�RSA��Կ���������� */
/* ע�⣺encBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int publicEncryptCert
(
   unsigned char *src,    /*Ҫ���ܵ�����*/
   int srcLen,            /*Ҫ���ܵ����ݵĳ���*/
   unsigned char *cert,   /*֤��*/
   int certLen,           /*֤�鳤��*/
   unsigned char **encBuf,/*���ܺ�����ݻ�����ָ��*/
   int *encBufLen         /*���ܺ�����ݵĳ���*/
);

/* ˵������֤���е�RSA��Կ���������� */
/* ע�⣺decBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
/* �����Ϊ��ʹ���߷����¼ӵĽӿ�*/
DLLENTRY int publicDecryptCert
(
   unsigned char *src,    /*Ҫ���ܵ�����*/
   int srcLen,            /*Ҫ���ܵ����ݵĳ���*/
   unsigned char *cert,   /*֤��*/
   int certLen,           /*֤�鳤��*/
   unsigned char **encBuf,/*���ܺ�����ݻ�����ָ��*/
   int *encBufLen         /*���ܺ�����ݵĳ���*/
);

/*********************************************************/
/*              part 2: Des�������                      */
/*********************************************************/

/* ˵������DES��/���ܽӿ� */
/* ����ֵ���ɹ� 0  ʧ�� ��0 */
/* ע�⣺encodedBuf�������ɽӿڷ��䣬���������ͷ�
         ���ĳ��Ȳ���8����ʱ���Զ���������ɸ�ֵΪ0x00���ֽڣ�
		 ���������ĳ��ȱ�8����������
*/
DLLENTRY int desEncDec
(
   unsigned char *src,    /*Ҫ���ܵ�����*/
   int  srcLen,           /*Ҫ���ܵ����ݵĳ���*/
   unsigned char *key,    /*��/���� ��Կ*/
   int  keyLen,           /*��/���� ��Կ�ĳ���*/
   int  flag,             /*1 for encryption, 0 for decryption*/
   unsigned char **encodedBuf,/*�����Ժ�����ݵĻ�����ָ��*/
   int *encodedBufLen         /*�����Ժ�����ݳ���*/
);

/* ˵��������һ������tripple DES��/���ܵ���Կ */
/* ע�⣺keyBuf�������ɽӿڷ��䣬���������ͷ�
   ����ֵ���ɹ� 0  ʧ�� ��0 */
DLLENTRY int genDESKey
(
   int length,            /*����Ҫ��������Կ�ĳ��� >= 24 */
   unsigned char *seed,   /*��������������*/
   unsigned char **keyBuf,/*��������Կ*/
   int *keyLen            /*��������Կ�ĳ���*/
);

#ifdef          __cplusplus
}
#endif

#endif
