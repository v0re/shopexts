cibverify.o:libcib.a

	gcc cibverify.c -o cibverify  -static -L. -lcib
	
cibsign.o:libcib.a 
	
	gcc cibsign.c -o cibsign -static -L. -lcib

libcib.a:ansix99.o qdes.o asc2bcd.o

	ar rcs libcib.a ansix99.o qdes.o asc2bcd.o

#libcib.so:ansix99.o qdes.o asc2bcd.o
#	
#	gcc ansix99.o qdes.o asc2bcd.o -fPIC -shared -o libcib.so

ansix99.o:ansix99.c
	
	gcc -c ansix99.c

qdes.o:qdes.c

	gcc -c qdes.c

asc2bcd.o:asc2bcd.c
	
	gcc -c asc2bcd.c

clean:
	rm -f *.o *.a *.so


