#!/usr/bin/perl -w
use strict;
use Math::BigInt;

my %RSA_CORE = (n=>2773,e=>63,d=>847); #p=47,q=59

my $N=new Math::BigInt($RSA_CORE{n});
my $E=new Math::BigInt($RSA_CORE{e});
my $D=new Math::BigInt($RSA_CORE{d});

print "N=$N D=$D E=$E\n";

sub RSA_ENCRYPT 
{
    my $r_mess = shift @_;
    my ($c,$i,$M,$C,$cmess);

    for($i=0;$i < length($$r_mess);$i++)
    {
        $c=ord(substr($$r_mess,$i,1));
        $M=Math::BigInt->new($c);
        $C=$M->copy(); $C->bmodpow($D,$N);
        $c=sprintf "%03X",$C;
        $cmess.=$c;
    }
    return \$cmess;
}

sub RSA_DECRYPT 
{
    my $r_mess = shift @_;
    my ($c,$i,$M,$C,$dmess);

    for($i=0;$i < length($$r_mess);$i+=3)
    {
        $c=substr($$r_mess,$i,3);
        $c=hex($c);
        $M=Math::BigInt->new($c);
        $C=$M->copy(); $C->bmodpow($E,$N);
        $c=chr($C);
        $dmess.=$c;
    }
    return \$dmess;
}

my $mess="rsa is suck";
$mess=$ARGV[0] if @ARGV >= 1;
print "source message: ",$mess," \n";

my $r_cmess = RSA_ENCRYPT(\$mess);
print "encrypt message : ",$$r_cmess,"\n";

my $r_dmess = RSA_DECRYPT($r_cmess);
print "decrypt message: ",$$r_dmess,"\n";

