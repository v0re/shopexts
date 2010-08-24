<?php exit(0);'<?xml version="1.0" encoding="UTF-8"?><request><query><method>store.trade.add</method><params>Array
(
    [tid] => 20100824122739
    [title] => Order Create
    [created] => 2010-08-24 12:09:18
    [modified] => 2010-08-24 12:09:18
    [status] => TRADE_ACTIVE
    [pay_status] => PAY_NO
    [ship_status] => SHIP_NO
    [has_invoice] => 
    [invoice_title] => 
    [invoice_fee] => 0.00
    [total_goods_fee] => 256.00
    [total_trade_fee] => 266.00
    [discount_fee] => 0.00
    [payed_fee] => 0.00
    [currency] => CNY
    [currency_rate] => 1.0000
    [total_currency_fee] => 266.00
    [buyer_obtain_point_fee] => 0
    [point_fee] => 0
    [total_weight] => 0
    [receiver_time] => 任意日期任意时间段
    [shipping_tid] => 1
    [shiptype_name] => nonused
    [shipping_fee] => 10.00
    [is_protect] => false
    [protect_fee] => 0.00
    [paytype_name] => 线下支付
    [is_cod] => false
    [receiver_name] => kkkk
    [receiver_email] => 
    [receiver_mobile] => 18601783181
    [receiver_state] => 上海
    [receiver_city] => 上海市
    [receiver_district] => 静安区
    [receiver_address] => 上海市静安区红旗路
    [receiver_zip] => 
    [receiver_phone] => 
    [commission_fee] => 0.00
    [trade_memo] => 
    [orders_number] => 1
    [orders] => {"order":[{"oid":2147483647,"type":"goods","type_alias":"\u5546\u54c1\u533a\u5757","iid":"1","title":"ShopEx\u7f51\u5e97\u4e3b\u673a\u6807\u51c6\u578b ShopEx\u4e3b\u673a","items_num":2,"order_status":"SHIP_NO","total_order_fee":"256.00","discount_fee":0,"consign_time":"","order_items":{"item":[{"sku_id":"1","iid":"1","bn":"P4C72F052E686E","name":"ShopEx\u7f51\u5e97\u4e3b\u673a\u6807\u51c6\u578b ShopEx\u4e3b\u673a","weight":"0.000","score":"0.00","price":128,"num":"2.00","sendnum":0,"total_item_fee":0,"item_type":"product"}]},"weight":0}]}
)
</params><rpc_callback><class>b2c_api_callback_app</class><method>callback</method><params>Array</params></rpc_callback></query><query><method>store.trade.payment.add</method><params>Array
(
    [tid] => 20100824122739
    [payment_id] => 12826313312111
    [seller_bank] => 线下支付
    [seller_account] => 1111
    [buyer_account] => 付款帐号
    [currency] => CNY
    [pay_fee] => 138
    [paycost] => 0.00
    [currency_fee] => 138.00
    [pay_type] => offline
    [payment_type] => 线下支付
    [t_begin] => 2010-08-24 14:28:51
    [t_end] => 2010-08-24 14:28:51
    [status] => SUCC
    [memo] => order
    [outer_no] => 
)
</params><rpc_callback><class>b2c_api_callback_app</class><method>callback</method><params>Array</params></rpc_callback></query></request>';