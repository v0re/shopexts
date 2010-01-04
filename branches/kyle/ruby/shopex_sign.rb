require 'rubygems'
require 'watir'
include Watir

@reg_user="kyle"
@reg_passwd="shopex"
@reg_email="kyle@shopcare.net"

ie=IE.new
#注册
ie.goto('http://shop25457.p02.shopex.cn/shopex484/?passport-signup.html')
ie.text_field(:id,"reg_user").set(@reg_user)
puts 'user name:'+@reg_user
ie.text_field(:id,"reg_passwd").set(@reg_passwd)
puts 'password:'+@reg_passwd
ie.text_field(:id,"reg_passwd_r").set(@reg_passwd)
puts 're-password:'+@reg_passwd
ie.text_field(:id,"reg_email").set(@reg_email)
puts 'email:'+@reg_email

ret = ie.button(:type,"submit").click
puts 'use time:'+ret.to_s

#登录
ie.goto('http://shop25457.p02.shopex.cn/shopex484/?passport-login.html')
ie.text_field(:id,"in_login").set(@reg_user)
ie.text_field(:id,"in_passwd").set(@reg_passwd)
ret = ie.button(:type,"submit").click
puts 'use time:'+ret.to_s

ie.close


