商户测试证书公钥:private.crt
商户测试证书私钥:private.key
商户私钥保护口令:12345678

提示：
以上证书只是作为验证开发API时使用，如果商户要和银行的模拟测试环境进行联调，需要当地行在模测测试环境录入商户信息，同时生成商户测试证书，不能直接使用此目录中的商户测试证书。

银行测试证书公钥:测试公钥publict.crt
银行生产证书公钥:生产公钥ebb2cpublic.crt

提示：
以上两张证书分别用于银行模测环境和生产环境对银行通知消息进行验签时使用。

模拟环境地址：

https://mybank3.dccnet.com.cn/servlet/ICBCINBSEBusinessServlet