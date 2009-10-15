#include <stdio.h>
#include <stdlib.h>

void bcd_to_asc(unsigned char *ascii_buf, unsigned char *bcd_buf,int conv_len, unsigned char type)
{
    int cnt;

    if (conv_len&0x01 && type) {cnt=1; conv_len++;}
    else cnt=0;
    for (; cnt<conv_len; cnt++, ascii_buf++){
	*ascii_buf = ((cnt&0x01) ? (*bcd_buf++&0x0f) : (*bcd_buf>>4));
	*ascii_buf += ((*ascii_buf>9) ? ('A'-10) : '0');
    }
}


int main(int argc, char *argv[])
{
  unsigned char *asc;
  unsigned char bcd[8];
  int len;
  
  //BCD to "A8EAB40EE70C96AC"
  bcd[0] = -88;
  bcd[1] = -22;
  bcd[2] = -76;
  bcd[3] = 14;
  bcd[4] = -25;
  bcd[5] = 12;
  bcd[6] = -106;
  bcd[7] = -84;
  
  asc = malloc(16);
  
  bcd_to_asc(asc,bcd,16,1);
  
  printf("%s", asc);    
  system("PAUSE");	
  return 0;
}
