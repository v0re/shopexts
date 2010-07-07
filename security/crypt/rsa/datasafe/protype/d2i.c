#include <stdio.h>
#include <openssl/rsa.h>
#include <openssl/sha.h>
#include <openssl/hmac.h>
#include <openssl/evp.h>
#include <openssl/bio.h>
#include <openssl/buffer.h>


int base64_decode(unsigned char *input, int length, char **outbuf) {
	BIO *bio, *b64, *bmem, *decoder;
	char inbuf[512];
	int inlen, readlen = -1;
	BUF_MEM *bptr;
	char * buffer;
	
	bmem = BIO_new_mem_buf(input, length);
	b64 = BIO_new(BIO_f_base64());
	
	decoder = BIO_push(b64, bmem);
	
	BIO_flush(decoder);
	BIO_get_mem_ptr(decoder, &bptr);
	
	if (buffer = (char *)malloc(length)) {
		readlen = BIO_read(decoder, buffer, length);
		buffer[readlen] = 0;
	}
	
	BIO_free_all(decoder);
	
	*outbuf = buffer;
	return readlen;
}

main(){
	
	unsigned char *pem_key_str = "MIGJAoGBAMMaorVRYpg6ZjzPFglnyoinCRFXytjk429mnriwgu8p7Kfr/YYVtdRR65xxqYUvwIGdO9VTukIfQgaT3TIYN6QQGhD+sOJwdz8+jm40LVKMPTA+dsoN+C4WVotlC7wwVJlL3MBIpnZmEZSwc5kN54s9dwlir7HwHS1GGanEHWrNAgMBAAEwggJdAgEAAoGBAMMaorVRYpg6ZjzPFglnyoinCRFXytjk429mnriwgu8p7Kfr/YYVtdRR65xxqYUvwIGdO9VTukIfQgaT3TIYN6QQGhD+sOJwdz8+jm40LVKMPTA+dsoN+C4WVotlC7wwVJlL3MBIpnZmEZSwc5kN54s9dwlir7HwHS1GGanEHWrNAgMBAAECgYBiKe6fp/khepCiG9eMl+oxY0mOrktjYZaFIG7Pog/e4Ysu2e/PHPFFiIoxRobyehoznLbUGLJoPm3r/U2XvNORGNuhI9Vf28m810r6+US4c7LHH8gu+zHZTk77480fpbjEo0hCIuez4iPtKpf7GRiaZ5pjXROtzRuWm3V7G3FAgQJBAPKKmLonUPoNSCEDM+uJ5xz+e3NxlUfpIwEANv5BuupmyKvTJ2FrfG6ZSpKBkql8U94/pq8H+O+4aHTHYX/uEH0CQQDN7ivck9mIGk4PjhkhfwaQvlW+3iYa6NOWViROnt/KIDrFHCQ4sDO60kaubzGQyGpgt2LEnlZZlnFxaHdubqSRAkEAm/Ts4f53/mHd+IRTtWgWOTmV7hSiNfw+at1Vf0aKx0DSVlJPZ0AzYfal0fEJenwcfbOWHcRVmOeOG1E9a4KMDQJABFE//injX6Udidny0O721kYHSi8iIWJMPVSlAjj2fChc0xEZ+U5IZ1xNOw79vQlWoZx2p6SiLtKmojgMePY84QJBAOhHyWKKR21TZdtv5P4kXNbo1OjnCfTN/l1mTePSUhdJxRBtxfVMYw684IOBVistUuq2sOJt3hV9vbpJgphyOgs=";
	
	RSA *pub_rsa,*priv_rsa;
	unsigned char buf[2048],*p;
	
	p=buf;
	base64_decode(pem_key_str,strlen(pem_key_str),p);
	
	/*
	p=buf;
	pub_rsa=d2i_RSAPublicKey(NULL,&p,(long)len);
	len-=(p-buf);
	priv_rsa=d2i_RSAPrivateKey(NULL,&p,(long)len);

	if ((pub_rsa == NULL) || (priv_rsa == NULL))
		ERR_print_errors_fp(stderr);

	RSA_free(pub_rsa);
	RSA_free(priv_rsa);
	*/

}

