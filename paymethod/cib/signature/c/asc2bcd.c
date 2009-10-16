#include <ctype.h>
#include "cib_api.h"

void asc_to_bcd(unsigned char *bcd_buf, unsigned char *ascii_buf,int conv_len, unsigned char type)
{
    int    cnt;
    char   ch, ch1;

	if (conv_len&0x01 && type ) ch1=0;
	else ch1=0x55;
	for (cnt=0; cnt<conv_len; ascii_buf++, cnt++) {
		if (*ascii_buf >= 'a' ) ch = *ascii_buf-'a' + 10;
		else if ( *ascii_buf >= 'A' ) ch =*ascii_buf- 'A' + 10;
		     else if ( *ascii_buf >= '0' ) ch =*ascii_buf-'0';
			  else ch = 0;
		if (ch1==0x55) ch1=ch;
		else {
			*bcd_buf++=ch1<<4 | ch;
			ch1=0x55;
		}
	}
	if (ch1!=0x55) *bcd_buf=ch1<<4;
}


void bcd_to_asc(unsigned char *ascii_buf, unsigned char *bcd_buf,int conv_len, unsigned char type)
{
    int cnt;

    if (conv_len&0x01 && type) 
    {
        cnt=1; 
        conv_len++;
    }
    else
    {
        cnt=0;
    }
    for (; cnt<conv_len; cnt++, ascii_buf++)
    {
	    *ascii_buf = ((cnt&0x01) ? (*bcd_buf++&0x0f) : (*bcd_buf>>4));
	    *ascii_buf += ((*ascii_buf>9) ? ('A'-10) : '0');
    }
}
