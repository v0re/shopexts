#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "cib_api.h"

main(int argc, unsigned char* argv[])
{
        int  len,i;
        char *mac_key,mac[9],*buf,*asc_buff;

        mac_key = argv[1];
        buf = argv[2];
        
        len = strlen(buf);
        
        ANSIX99(mac_key, buf, len, mac); 
        asc_buff = malloc(16);
        memset( asc_buff, 0, 16);
        bcd_to_asc( asc_buff, mac, 16, 1 );
        memset( mac, 0, 9);
        strncpy(mac, asc_buff, 8);
        printf( "%s", mac );

}
