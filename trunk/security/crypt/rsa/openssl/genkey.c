/*gcc genkey.c -o genkey -lcrypto
 *
 */
#include <stdio.h>
#include <openssl/err.h>
#include <openssl/rsa.h>
#include <openssl/pem.h>

#define SECFILE "sec.pem"
#define PUBFILE "pub.pem"

int main(int argc, char *argv[])
{
	int ret = -1, keylen = 0;
	RSA *key;
	FILE *fp;

	if (argc != 2) {
		fprintf(stderr, "Error: too many/few arguments.\n "
			"Usage: %s <numbits>\n", argv[0]);
		goto out;
	}
	keylen = atoi(argv[1]);
	if ((key = RSA_generate_key(keylen, 3, NULL, NULL)) == NULL) {
		fprintf(stderr, "%s\n",
			ERR_error_string(ERR_get_error(), NULL));
		goto out;
	}
	if (!RSA_check_key(key)) {
		fprintf(stderr,
			"Error: Problems while generating RSA Key.\nRetry.\n");
		goto out_free;
	}
	fp = fopen(SECFILE, "w");
	if (!fp) {
		goto out_free;
	}
	if (!PEM_write_RSAPrivateKey(fp, key, NULL, NULL, 0, 0, NULL)) {
		fprintf(stderr,
			"Error: problems while writing RSA Private Key.\n");
		goto out_close;
	}
	fclose(fp);
	fp = fopen(PUBFILE, "w");
	if (!fp) {
		goto out_free;
	}
	if (!PEM_write_RSAPublicKey(fp, key)) {
		fprintf(stderr,
			"Error: problems while writing RSA Public Key.\n");
		goto out_close;
	}
	printf("RSA key generated.\nLenght = %d bits.\n", keylen);
	ret = 0;
 out_close:
	fclose(fp);
 out_free:
	RSA_free(key);
 out:
	return ret;
}

