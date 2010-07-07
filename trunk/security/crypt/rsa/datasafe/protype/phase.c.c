/*
* gcc -lcrypto phase.c.c -o phase.c
*/

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <openssl/bio.h>

#define pubkey  "-----BEGIN RSA PUBLIC KEY-----
MIGHAoGBALagXIxAJkQ7XDnBsWlIXVc8/mrKYN87D2yOdZq9j7B8b1IZEXnobrn9
nR9NdxSmEfQkYXG3TaTjD5k2BErEOicY7TvoXk3ReQmYv7Milz8mz/f+/eqQq/gK
Ki6VY17lyyF4ZAPcAusdcXYPRWoUerC6KiC33r+9W90eCX0HVrDHAgED
-----END RSA PUBLIC KEY-----"

#define prikey "-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQC2oFyMQCZEO1w5wbFpSF1XPP5qymDfOw9sjnWavY+wfG9SGRF5
6G65/Z0fTXcUphH0JGFxt02k4w+ZNgRKxDonGO076F5N0XkJmL+zIpc/Js/3/v3q
kKv4CioulWNe5csheGQD3ALrHXF2D0VqFHqwuiogt96/vVvdHgl9B1awxwIBAwKB
gHnAPbLVbtgnktEry5uFk499/vHcQJTSCkhe+RHTtSBS9OFmC6aa9Hv+aL+I+g3E
C/gYQPZ6M8NCCmYkAtyC0W5FIjA+KB2NCEOgOw5BeiLMsqGOUHxi1HaJ2NvhSX4h
8y/xARPLiVqfrhDSOgxVfFQ4Y6WEu796/hwpqJcbbitLAkEA5ZwZORyM+1h+EBmz
kFpHcVaqu7Z1DpzC11xQ362UUsrPxcsH0w1+7lGbqkYU/RiE+g4nz8gVpSCmPfnn
WsS6DwJBAMud2lIFlIKUJhhNajAFw4JtWu3Ouu3Qg2QIk+PHjWATickXXlenZD+a
wSmoQpcnrWuGgKD9Ct++DKGSswNstckCQQCZErt7aF385algESJgPC+g5HHSeaNf
EyyPkuCVHmLh3IqD3K/iCP9Ji70cLriouwNRXsU1MA5uFcQpUUTnLdFfAkEAh76R
jAO4Vw1uut5Gyq6CVvOR898nSTWs7VsNQoUI6rexMLo+5RpC1RHWG8WBuhpznQRV
wKix6n6zFmHMrPMj2wJAKxAfmh6q64AaTJeeACAo1+gj0kiQlj4Jq2mMKZbBuw0m
pH59lifH3Q9br9NlWt39pEA+JpSQIYxNlGXD8Glzdw==
-----END RSA PRIVATE KEY-----"

 
main(){
	
}
