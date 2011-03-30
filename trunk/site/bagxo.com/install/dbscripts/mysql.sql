#$Rev: 21246 $

drop table if exists sdb_admin_roles;

drop table if exists sdb_advance_freeze;

drop table if exists sdb_advance_logs;

drop table if exists sdb_api;

drop table if exists sdb_articles;

drop table if exists sdb_brand;

drop table if exists sdb_cachemgr;

drop table if exists sdb_command_list;

drop table if exists sdb_comments;

drop table if exists sdb_coupons;

drop table if exists sdb_coupons_p_items;

drop table if exists sdb_coupons_u_items;

drop table if exists sdb_currency;

drop table if exists sdb_delivery;

drop table if exists sdb_delivery_item;

drop table if exists sdb_dly_area;

drop table if exists sdb_dly_center;

drop table if exists sdb_dly_corp;

drop table if exists sdb_dly_h_area;

drop table if exists sdb_dly_type;

drop table if exists sdb_event_hdls;

drop table if exists sdb_gift;

drop table if exists sdb_gift_cat;

drop table if exists sdb_gift_items;

drop table if exists sdb_gimages;

drop table if exists sdb_gnotify;

drop table if exists sdb_goods;

drop table if exists sdb_goods_cat;

drop table if exists sdb_goods_keywords;

drop table if exists sdb_goods_lv_price;

drop table if exists sdb_goods_memo;

drop table if exists sdb_goods_rate;

drop table if exists sdb_goods_spec_index;

drop table if exists sdb_goods_type;

drop table if exists sdb_goods_type_spec;

drop table if exists sdb_goods_virtual_cat;

drop table if exists sdb_link;

drop table if exists sdb_lnk_acts;

drop table if exists sdb_lnk_roles;

drop table if exists sdb_logs;

drop table if exists sdb_member_addrs;

drop table if exists sdb_member_attr;

drop table if exists sdb_member_coupon;

drop table if exists sdb_member_dealer;

drop table if exists sdb_member_lv;

drop table if exists sdb_member_mattrvalue;

drop table if exists sdb_members;

drop table if exists sdb_message;

drop table if exists sdb_msgqueue;

drop table if exists sdb_op_sessions;

drop table if exists sdb_operators;

drop table if exists sdb_order_archives;

drop table if exists sdb_order_items;

drop table if exists sdb_order_log;

drop table if exists sdb_order_pmt;

drop table if exists sdb_order_tmpl;

drop table if exists sdb_orders;

drop table if exists sdb_package;

drop table if exists sdb_package_product;

drop table if exists sdb_pages;

drop table if exists sdb_passport_cfg;

drop table if exists sdb_payment_cfg;

drop table if exists sdb_payments;

drop table if exists sdb_pdt_actions;

drop table if exists sdb_pmt_gen_coupon;

drop table if exists sdb_pmt_goods;

drop table if exists sdb_pmt_goods_cat;

drop table if exists sdb_pmt_member_lv;

drop table if exists sdb_point_history;

drop table if exists sdb_print_tmpl;

drop table if exists sdb_product_memo;

drop table if exists sdb_products;

drop table if exists sdb_promotion;

drop table if exists sdb_promotion_activity;

drop table if exists sdb_promotion_scheme;

drop table if exists sdb_pub_files;

drop table if exists sdb_refunds;

drop table if exists sdb_regions;

drop table if exists sdb_return_product;

drop table if exists sdb_sell_logs;

drop table if exists sdb_sendbox;

drop table if exists sdb_settings;

drop table if exists sdb_sfiles;

drop table if exists sdb_sitemaps;

drop table if exists sdb_spec_values;

drop table if exists sdb_specification;

drop table if exists sdb_status;

drop table if exists sdb_supplier_goods_delete;

drop table if exists sdb_supplier_sync;

drop table if exists sdb_systmpl;

drop table if exists sdb_tag_rel;

drop table if exists sdb_tags;

drop table if exists sdb_themes;

drop table if exists sdb_type_brand;

drop table if exists sdb_wholesale;

drop table if exists sdb_wholesale_single;

drop table if exists sdb_widgets_set;

drop table if exists sdb_ws_goods;

drop table if exists sdb_ws_goods_cat;

/*==============================================================*/
/* Table: sdb_admin_roles                                       */
/*==============================================================*/
create table sdb_admin_roles
(
    
   role_id                        int unsigned                   not null AUTO_INCREMENT,
    
   role_name                      varchar(100)                   not null,
    
   role_memo                      text                           not null,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (role_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_advance_freeze                                    */
/*==============================================================*/
create table sdb_advance_freeze
(
    
   freeze_id                      mediumint(8)                   not null AUTO_INCREMENT,
    
   member_id                      mediumint(8)                   not null,
    
   freeze_money                   decimal(20,3)                  not null default 0.00,
    
   thaw_money                     decimal(20,3)                  not null default 0.00,
    
   created                        int(10)                        not null,
    
   message                        varchar(255),
    
   shop_message                   varchar(255),
    
   disabled                       enum('true','false')           default 'false',
   primary key (freeze_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_advance_logs                                      */
/*==============================================================*/
create table sdb_advance_logs
(
    
   log_id                         mediumint unsigned             not null AUTO_INCREMENT,
    
   member_id                      mediumint unsigned             not null,
    
   money                          decimal(20,3)                  not null,
    
   message                        varchar(255),
    
   mtime                          int unsigned                   not null,
    
   payment_id                     varchar(20),
    
   order_id                       varchar(20),
    
   paymethod                      varchar(100),
    
   memo                           varchar(100),
    
   import_money                   decimal(20,3)                  not null default 0,
    
   explode_money                  decimal(20,3)                  not null default 0,
    
   member_advance                 decimal(20,3)                  not null default 0,
    
   shop_advance                   decimal(20,3)                  not null default 0,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (log_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_mtime                                             */
/*==============================================================*/
create index ind_mtime on sdb_advance_logs
(
   mtime
)
/*tbindex
 array('sdb_advance_logs'=>
  array(
      'name'=>"ind_mtime", 'colum'=>array("mtime")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_advance_logs
(
   disabled
)
/*tbindex
 array('sdb_advance_logs'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_api                                               */
/*==============================================================*/
create table sdb_api
(
    
   api_id                         mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   client_id                      varchar(38),
    
   salt                           varchar(64),
    
   description                    varchar(200),
    
   allow_command                  longtext,
    
   enable                         tinyint(1)                     default 1,
   primary key (api_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_articles                                          */
/*==============================================================*/
create table sdb_articles
(
    
   article_id                     mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   node_id                        mediumint unsigned             not null,
    
   title                          varchar(200),
    
   content                        longtext,
    
   uptime                         int(10),
    
   ifpub                          tinyint(1),
    
   align                          varchar(12),
    
   filetype                       varchar(15),
    
   filename                       varchar(80),
    
   orderlist                      mediumint(6),
    
   disabled                       enum('true','false')           default 'false',
   primary key (article_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_articles
(
   disabled
)
/*tbindex
 array('sdb_articles'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_brand                                             */
/*==============================================================*/
create table sdb_brand
(
    
   brand_id                       mediumint unsigned             not null AUTO_INCREMENT,
    
   supplier_id                    mediumint unsigned,
    
   supplier_brand_id              mediumint(8) unsigned          default 0,
    
   brand_name                     varchar(50),
    
   brand_url                      varchar(255),
    
   brand_desc                     longtext,
    
   brand_logo                     varchar(255),
    
   brand_keywords                 longtext,
    
   disabled                       enum('true','false')           default 'false',
    
   ordernum                       mediumint(8) unsigned,
   primary key (brand_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_brand
(
   disabled
)
/*tbindex
 array('sdb_brand'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_cachemgr                                          */
/*==============================================================*/
create table sdb_cachemgr
(
    
   cname                          varchar(30)                    not null,
    
   modified                       int unsigned                   not null,
   primary key (cname)
)
type = HEAP DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_command_list                                      */
/*==============================================================*/
create table sdb_command_list
(
    
   cmd_action                     varchar(100)                   not null,
    
   supplier_goods_id              int                            not null,
    
   cmd_info                       longtext,
    
   supplier_id                    int                            not null,
    
   cmd_lasttime                   int,
    
   goods_name                     varchar(255),
   primary key (cmd_action, supplier_goods_id, supplier_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_comments                                          */
/*==============================================================*/
create table sdb_comments
(
    
   comment_id                     mediumint unsigned             not null AUTO_INCREMENT,
    
   for_comment_id                 mediumint unsigned,
    
   goods_id                       mediumint unsigned             not null,
    
   object_type                    enum('ask','discuss','buy')    not null default 'ask',
    
   author_id                      mediumint unsigned,
    
   author                         varchar(100),
    
   levelname                      varchar(50),
    
   contact                        varchar(255),
    
   mem_read_status                enum('false','true')           not null default 'false',
    
   adm_read_status                enum('false','true')           not null default 'false',
    
   time                           int(10)                        not null,
    
   lastreply                      int(10)                        not null,
    
   reply_name                     varchar(100),
    
   title                          varchar(255),
    
   comment                        longtext,
    
   ip                             varchar(15),
    
   display                        enum('false','true')           not null default 'false',
    
   p_index                        tinyint(2),
    
   disabled                       enum('false','true')           default 'false',
   primary key (comment_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_goods                                             */
/*==============================================================*/
create index ind_goods on sdb_comments
(
   goods_id
)
/*tbindex
 array('sdb_comments'=>
  array(
      'name'=>"ind_goods", 'colum'=>array("goods_id")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_member                                            */
/*==============================================================*/
create index ind_member on sdb_comments
(
   author_id
)
/*tbindex
 array('sdb_comments'=>
  array(
      'name'=>"ind_member", 'colum'=>array("author_id")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_coupons                                           */
/*==============================================================*/
create table sdb_coupons
(
    
   cpns_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   cpns_name                      varchar(255),
    
   pmt_id                         mediumint unsigned,
    
   cpns_prefix                    varchar(50)                    not null,
    
   cpns_gen_quantity              int(8) unsigned                not null default 0,
    
   cpns_key                       varchar(20)                    not null,
    
   cpns_status                    enum('0','1')                  not null default '1',
    
   cpns_type                      enum('0','1','2')              not null default '1',
    
   cpns_point                     int(10)                        default null,
    
   disabled                       enum('true','false')           default 'false',
   primary key (cpns_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_coupons
(
   disabled
)
/*tbindex
 array('sdb_coupons'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_coupons_p_items                                   */
/*==============================================================*/
create table sdb_coupons_p_items
(
    
   order_id                       bigint unsigned                not null,
    
   cpns_id                        mediumint unsigned             not null,
    
   cpns_name                      varchar(255),
    
   nums                           mediumint unsigned,
   primary key (order_id, cpns_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_coupons_u_items                                   */
/*==============================================================*/
create table sdb_coupons_u_items
(
    
   order_id                       bigint unsigned                not null,
    
   cpns_id                        mediumint unsigned             not null,
    
   cpns_name                      varchar(255),
    
   memc_code                      varchar(255),
    
   cpns_type                      enum('0','1','2'),
   primary key (order_id, cpns_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_currency                                          */
/*==============================================================*/
create table sdb_currency
(
    
   cur_name                       varchar(20)                    not null,
    
   cur_code                       varchar(8)                     not null,
    
   cur_sign                       varchar(5),
    
   cur_rate                       decimal(10,4)                  not null default 1.0000,
    
   def_cur                        enum('true','false')           not null,
    
   disabled                       enum('true','false')           default 'false',
   primary key (cur_code)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_currency
(
   disabled
)
/*tbindex
 array('sdb_currency'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_delivery                                          */
/*==============================================================*/
create table sdb_delivery
(
    
   delivery_id                    bigint unsigned                not null,
    
   order_id                       bigint unsigned,
    
   member_id                      mediumint unsigned,
    
   money                          decimal(20,3)                  not null,
    
   type                           enum('return','delivery')      not null default 'delivery',
    
   is_protect                     enum('true','false')           not null default 'false',
    
   delivery                       varchar(20),
    
   logi_id                        varchar(50),
    
   logi_name                      varchar(100),
    
   logi_no                        varchar(50),
    
   ship_name                      varchar(50),
    
   ship_area                      varchar(255),
    
   ship_addr                      varchar(100),
    
   ship_zip                       varchar(20),
    
   ship_tel                       varchar(30),
    
   ship_mobile                    varchar(50),
    
   ship_email                     varchar(150),
    
   t_begin                        int(10),
    
   t_end                          int(10),
    
   op_name                        varchar(50),
    
   status                         enum('succ','failed','cancel','lost','progress','timeout','ready') not null default 'ready',
    
   memo                           longtext,
    
   disabled                       enum('true','false')           default 'false',
   primary key (delivery_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_delivery
(
   disabled
)
/*tbindex
 array('sdb_delivery'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_delivery_item                                     */
/*==============================================================*/
create table sdb_delivery_item
(
    
   item_id                        int unsigned                   not null AUTO_INCREMENT,
    
   delivery_id                    bigint unsigned                not null,
    
   item_type                      enum('goods','gift','pkg')     not null default 'goods',
    
   product_id                     bigint unsigned                not null,
    
   product_bn                     varchar(30),
    
   product_name                   varchar(200),
    
   number                         int(10)                        not null,
   primary key (item_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_dly_area                                          */
/*==============================================================*/
create table sdb_dly_area
(
    
   area_id                        mediumint(6) unsigned          not null AUTO_INCREMENT,
    
   name                           varchar(80)                    not null,
    
   disabled                       enum('true','false')           default 'false',
    
   ordernum                       smallint(4) unsigned,
   primary key (area_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_dly_area
(
   disabled
)
/*tbindex
 array('sdb_dly_area'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_dly_center                                        */
/*==============================================================*/
create table sdb_dly_center
(
    
   dly_center_id                  int unsigned                   not null AUTO_INCREMENT,
    
   name                           varchar(50)                    not null,
    
   address                        varchar(200),
    
   region                         varchar(100),
    
   zip                            varchar(20),
    
   phone                          varchar(100),
    
   uname                          varchar(100),
    
   cellphone                      varchar(100),
    
   sex                            enum('male','famale'),
    
   memo                           longtext,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (dly_center_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_dly_center
(
   disabled
)
/*tbindex
 array('sdb_dly_center'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_dly_corp                                          */
/*==============================================================*/
create table sdb_dly_corp
(
    
   corp_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   type                           varchar(6),
    
   name                           varchar(200),
    
   disabled                       enum('true','false')           default 'false',
    
   ordernum                       smallint(4) unsigned,
    
   website                        varchar(200),
   primary key (corp_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_type                                              */
/*==============================================================*/
create index ind_type on sdb_dly_corp
(
   type
)
/*tbindex
 array('sdb_dly_corp'=>
  array(
      'name'=>"ind_type", 'colum'=>array("type")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_dly_corp
(
   disabled
)
/*tbindex
 array('sdb_dly_corp'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_dly_h_area                                        */
/*==============================================================*/
create table sdb_dly_h_area
(
    
   dha_id                         mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   dt_id                          mediumint(8) unsigned,
    
   area_id                        mediumint(6) unsigned          default 0,
    
   price                          varchar(100)                   default '0',
    
   has_cod                        tinyint(1) unsigned            not null default 0,
    
   areaname_group                 longtext,
    
   areaid_group                   longtext,
    
   config                         varchar(255),
    
   expressions                    varchar(255),
    
   ordernum                       smallint(4) unsigned,
   primary key (dha_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_dly_type                                          */
/*==============================================================*/
create table sdb_dly_type
(
    
   dt_id                          mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   dt_name                        varchar(50),
    
   dt_config                      longtext,
    
   dt_expressions                 longtext,
    
   detail                         longtext,
    
   price                          longtext                       not null,
    
   type                           tinyint(1) unsigned            not null default 1,
    
   gateway                        mediumint(8) unsigned          default 0,
    
   protect                        tinyint(1) unsigned            not null default 0,
    
   protect_rate                   float(6,3),
    
   ordernum                       smallint(4)                    default 0,
    
   has_cod                        tinyint(1) unsigned            not null default 0,
    
   minprice                       float(10,2)                    not null default 0.00,
    
   disabled                       enum('true','false')           default 'false',
    
   corp_id                        int(10),
    
   dt_status                      mediumint(1) unsigned,
   primary key (dt_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_dly_type
(
   disabled
)
/*tbindex
 array('sdb_dly_type'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_event_hdls                                        */
/*==============================================================*/
create table sdb_event_hdls
(
    
   handle_id                      int unsigned                   not null AUTO_INCREMENT,
    
   target                         varchar(50)                    not null,
    
   event                          varchar(50)                    not null,
    
   file                           varchar(255)                   not null,
    
   class                          varchar(50),
    
   func                           varchar(50)                    not null,
    
   orderby                        smallint unsigned,
    
   setting                        longtext,
    
   disabled                       enum('1','0')                  not null default '0',
   primary key (handle_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_gift                                              */
/*==============================================================*/
create table sdb_gift
(
    
   gift_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   giftcat_id                     mediumint unsigned,
    
   insert_time                    int(10)                        not null default 0,
    
   update_time                    int(10)                        not null default 0,
    
   name                           varchar(255),
    
   thumbnail_pic                  varchar(255),
    
   small_pic                      varchar(255),
    
   big_pic                        varchar(255),
    
   image_file                     longtext,
    
   intro                          varchar(255),
    
   gift_describe                  longtext,
    
   weight                         int,
    
   storage                        mediumint unsigned             default 0,
    
   price                          varchar(255)                   default null,
    
   orderlist                      mediumint(8) unsigned          default 0,
    
   shop_iffb                      enum('0','1')                  not null default '1',
    
   limit_num                      mediumint unsigned             default 0,
    
   limit_start_time               int(10),
    
   limit_end_time                 int(10),
    
   limit_level                    varchar(255)                   default null,
    
   ifrecommend                    enum('0','1')                  not null default '0',
    
   point                          mediumint unsigned             default '0',
    
   freez                          mediumint unsigned             default '0',
    
   disabled                       enum('true','false')           default 'false',
   primary key (gift_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_gift
(
   disabled
)
/*tbindex
 array('sdb_gift'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_gift_cat                                          */
/*==============================================================*/
create table sdb_gift_cat
(
    
   giftcat_id                     mediumint unsigned             not null AUTO_INCREMENT,
    
   cat                            varchar(255),
    
   orderlist                      mediumint(6) unsigned,
    
   shop_iffb                      enum('0','1')                  default '1',
    
   disabled                       enum('true','false')           default 'false',
   primary key (giftcat_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_gift_cat
(
   disabled
)
/*tbindex
 array('sdb_gift_cat'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_gift_items                                        */
/*==============================================================*/
create table sdb_gift_items
(
    
   order_id                       bigint unsigned                not null,
    
   gift_id                        mediumint unsigned             not null,
    
   name                           varchar(200),
    
   point                          int(8),
    
   nums                           mediumint unsigned,
    
   amount                         int unsigned,
    
   sendnum                        mediumint unsigned             default 0,
    
   getmethod                      enum('present','exchange')     not null default 'present',
   primary key (order_id, gift_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_gimages                                           */
/*==============================================================*/
create table sdb_gimages
(
    
   gimage_id                      mediumint unsigned             not null AUTO_INCREMENT,
    
   goods_id                       mediumint unsigned,
    
   is_remote                      enum('true','false')           not null default 'false',
    
   source                         varchar(100)                   not null,
    
   orderby                        tinyint unsigned               not null default 0,
    
   src_size_width                 int unsigned                   not null,
    
   src_size_height                int unsigned                   not null,
    
   small                          varchar(100),
    
   big                            varchar(100),
    
   thumbnail                      varchar(100),
    
   up_time                        int unsigned                   not null,
   primary key (gimage_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_gnotify                                           */
/*==============================================================*/
create table sdb_gnotify
(
    
   gnotify_id                     mediumint unsigned             not null AUTO_INCREMENT,
    
   goods_id                       mediumint unsigned,
    
   member_id                      mediumint unsigned,
    
   product_id                     mediumint unsigned,
    
   email                          varchar(200),
    
   status                         enum('ready','send','progress') not null default 'ready',
    
   send_time                      int unsigned,
    
   creat_time                     int unsigned,
    
   disabled                       enum('false','true')           not null default 'false',
    
   remark                         longtext,
   primary key (gnotify_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_goods                                             */
/*==============================================================*/
create index ind_goods on sdb_gnotify
(
   goods_id,
   product_id,
   member_id
)
/*tbindex
 array('sdb_gnotify'=>
  array(
      'name'=>"ind_goods", 'colum'=>array("goods_id,
product_id,
member_id")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_gnotify
(
   disabled
)
/*tbindex
 array('sdb_gnotify'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_goods                                             */
/*==============================================================*/
create table sdb_goods
(
    
   goods_id                       mediumint unsigned             not null AUTO_INCREMENT,
    
   cat_id                         mediumint unsigned             not null,
    
   type_id                        mediumint unsigned,
    
   goods_type                     enum('normal','bind')          not null default 'normal',
    
   brand_id                       mediumint unsigned,
    
   brand                          varchar(100),
    
   supplier_id                    mediumint unsigned,
    
   supplier_goods_id              mediumint unsigned,
    
   ws_policy                      enum('11','01')                not null default '01',
    
   wss_params                     longtext,
    
   image_default                  longtext,
    
   udfimg                         enum('true','false')           default 'false',
    
   thumbnail_pic                  varchar(255),
    
   small_pic                      varchar(255),
    
   big_pic                        varchar(255),
    
   image_file                     longtext,
    
   brief                          varchar(255),
    
   intro                          longtext,
    
   mktprice                       decimal(20,3),
    
   cost                           decimal(20,3)                  not null default 0,
    
   price                          decimal(20,3)                  not null default 0,
    
   bn                             varchar(200),
    
   name                           varchar(200)                   not null,
    
   marketable                     enum('true','false')           not null default 'true',
    
   weight                         decimal(20,3),
    
   unit                           varchar(20),
    
   store                          mediumint unsigned,
    
   score_setting                  enum('percent','number')       default 'number',
    
   score                          mediumint unsigned,
    
   spec                           longtext,
    
   pdt_desc                       longtext,
    
   spec_desc                      longtext,
    
   params                         longtext,
    
   uptime                         int(10),
    
   downtime                       int(10),
    
   last_modify                    int(10),
    
   disabled                       enum('true','false')           not null default 'false',
    
   notify_num                     mediumint unsigned             not null default 0,
    
   rank                           decimal(5,3)                   default 5,
    
   rank_count                     int unsigned                   default 0,
    
   comments_count                 int unsigned                   not null default 0,
    
   view_w_count                   int unsigned                   not null default 0,
    
   view_count                     int unsigned                   not null default 0,
    
   buy_count                      int unsigned                   not null default 0,
    
   buy_w_count                    int unsigned                   not null default 0,
    
   count_stat                     longtext,
    
   p_order                        mediumint                      not null default 30,
    
   d_order                        mediumint                      not null default 30,
    
   p_1                            mediumint unsigned,
    
   p_2                            mediumint unsigned,
    
   p_3                            mediumint unsigned,
    
   p_4                            mediumint unsigned,
    
   p_5                            mediumint unsigned,
    
   p_6                            mediumint unsigned,
    
   p_7                            mediumint unsigned,
    
   p_8                            mediumint unsigned,
    
   p_9                            mediumint unsigned,
    
   p_10                           mediumint unsigned,
    
   p_11                           mediumint unsigned,
    
   p_12                           mediumint unsigned,
    
   p_13                           mediumint unsigned,
    
   p_14                           mediumint unsigned,
    
   p_15                           mediumint unsigned,
    
   p_16                           mediumint unsigned,
    
   p_17                           mediumint unsigned,
    
   p_18                           mediumint unsigned,
    
   p_19                           mediumint unsigned,
    
   p_20                           mediumint unsigned,
    
   p_21                           varchar(255),
    
   p_22                           varchar(255),
    
   p_23                           varchar(255),
    
   p_24                           varchar(255),
    
   p_25                           varchar(255),
    
   p_26                           varchar(255),
    
   p_27                           varchar(255),
    
   p_28                           varchar(255),
    
   goods_info_update_status       enum('true','false')           default 'false',
    
   stock_update_status            enum('true','false')           default 'false',
    
   marketable_update_status       enum('true','false')           default 'false',
    
   img_update_status              enum('true','false')           default 'false',
   primary key (goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: uni_bn                                                */
/*==============================================================*/
create unique index uni_bn on sdb_goods
(
   bn
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"uni_bn", 'colum'=>array("bn")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_1                                               */
/*==============================================================*/
create index ind_p_1 on sdb_goods
(
   p_1
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_1", 'colum'=>array("p_1")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_2                                               */
/*==============================================================*/
create index ind_p_2 on sdb_goods
(
   p_2
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_2", 'colum'=>array("p_2")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_3                                               */
/*==============================================================*/
create index ind_p_3 on sdb_goods
(
   p_3
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_3", 'colum'=>array("p_3")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_4                                               */
/*==============================================================*/
create index ind_p_4 on sdb_goods
(
   p_4
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_4", 'colum'=>array("p_4")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_23                                              */
/*==============================================================*/
create index ind_p_23 on sdb_goods
(
   p_23
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_23", 'colum'=>array("p_23")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_22                                              */
/*==============================================================*/
create index ind_p_22 on sdb_goods
(
   p_22
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_22", 'colum'=>array("p_22")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_21                                              */
/*==============================================================*/
create index ind_p_21 on sdb_goods
(
   p_21
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_p_21", 'colum'=>array("p_21")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_frontend                                          */
/*==============================================================*/
create index ind_frontend on sdb_goods
(
   disabled,
   goods_type,
   marketable
)
/*tbindex
 array('sdb_goods'=>
  array(
      'name'=>"ind_frontend", 'colum'=>array("disabled,
goods_type,
marketable")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_goods_cat                                         */
/*==============================================================*/
create table sdb_goods_cat
(
    
   cat_id                         mediumint unsigned             not null AUTO_INCREMENT,
    
   parent_id                      mediumint unsigned,
    
   supplier_id                    mediumint unsigned,
    
   supplier_cat_id                mediumint unsigned,
    
   cat_path                       varchar(100)                   default ',',
    
   is_leaf                        enum('true','false')           not null,
    
   type_id                        mediumint,
    
   cat_name                       varchar(100)                   not null,
    
   disabled                       enum('true','false')           not null default 'false',
    
   p_order                        mediumint unsigned,
    
   goods_count                    mediumint unsigned,
    
   tabs                           longtext,
    
   finder                         longtext,
    
   addon                          longtext,
    
   child_count                    mediumint unsigned             not null default 0,
   primary key (cat_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_cat_path                                          */
/*==============================================================*/
create index ind_cat_path on sdb_goods_cat
(
   cat_path
)
/*tbindex
 array('sdb_goods_cat'=>
  array(
      'name'=>"ind_cat_path", 'colum'=>array("cat_path")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_goods_cat
(
   disabled
)
/*tbindex
 array('sdb_goods_cat'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_goods_keywords                                    */
/*==============================================================*/
create table sdb_goods_keywords
(
    
   goods_id                       mediumint unsigned             not null,
    
   keyword                        varchar(40)                    not null default '',
   primary key (keyword, goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_goods_lv_price                                    */
/*==============================================================*/
create table sdb_goods_lv_price
(
    
   product_id                     mediumint unsigned             not null default 0,
    
   level_id                       mediumint unsigned             not null,
    
   goods_id                       mediumint unsigned             not null default 0,
    
   price                          decimal(20,3),
   primary key (product_id, level_id, goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_goods_memo                                        */
/*==============================================================*/
create table sdb_goods_memo
(
    
   goods_id                       mediumint unsigned             not null,
    
   p_key                          varchar(20)                    not null,
    
   p_value                        longtext,
   primary key (goods_id, p_key)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_goods_rate                                        */
/*==============================================================*/
create table sdb_goods_rate
(
    
   goods_1                        mediumint unsigned             not null,
    
   goods_2                        mediumint unsigned             not null,
    
   manual                         enum('left','both')            default NULL,
    
   rate                           mediumint unsigned             not null default 1,
   primary key (goods_1, goods_2)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_goods_spec_index                                  */
/*==============================================================*/
create table sdb_goods_spec_index
(
    
   type_id                        mediumint unsigned             not null default 0,
    
   spec_id                        mediumint unsigned             not null default 0,
    
   spec_value_id                  mediumint unsigned             not null default 0,
    
   spec_value                     varchar(100)                   not null default '',
    
   goods_id                       mediumint unsigned             not null default 0,
    
   product_id                     mediumint unsigned             not null default 0,
   primary key (spec_value_id, spec_value, product_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: type_specvalue_index                                  */
/*==============================================================*/
create index type_specvalue_index on sdb_goods_spec_index
(
   type_id,
   spec_value_id,
   goods_id
)
/*tbindex
 array('sdb_goods_spec_index'=>
  array(
      'name'=>"type_specvalue_index", 'colum'=>array("type_id,
spec_value_id,
goods_id")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_goods_type                                        */
/*==============================================================*/
create table sdb_goods_type
(
    
   type_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   name                           varchar(100)                   not null,
    
   alias                          longtext,
    
   is_physical                    enum('0','1')                  not null default '1',
    
   supplier_id                    mediumint unsigned,
    
   supplier_type_id               mediumint unsigned,
    
   schema_id                      varchar(30)                    not null,
    
   props                          longtext,
    
   spec                           longtext,
    
   setting                        longtext,
    
   minfo                          longtext,
    
   params                         longtext,
    
   dly_func                       enum('0','1')                  not null default '0',
    
   ret_func                       enum('0','1')                  not null default '0',
    
   reship                         enum('disabled','func','normal','mixed') not null default 'normal',
    
   disabled                       enum('true','false')           default 'false',
    
   is_def                         enum('true','false')           not null default 'false',
   primary key (type_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_goods_type
(
   disabled
)
/*tbindex
 array('sdb_goods_type'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_goods_type_spec                                   */
/*==============================================================*/
create table sdb_goods_type_spec
(
    
   spec_id                        mediumint unsigned             default 0,
    
   type_id                        mediumint unsigned             default 0,
    
   spec_style                     enum('select','flat','disabled') not null default 'flat'
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_goods_virtual_cat                                 */
/*==============================================================*/
create table sdb_goods_virtual_cat
(
    
   virtual_cat_id                 mediumint unsigned             not null AUTO_INCREMENT,
    
   virtual_cat_name               varchar(100)                   not null,
    
   filter                         longtext,
    
   addon                          longtext,
    
   type_id                        mediumint unsigned,
    
   disabled                       enum('false','true')           not null default 'false',
    
   parent_id                      mediumint unsigned             default 0,
    
   cat_id                         mediumint unsigned,
    
   p_order                        mediumint unsigned,
    
   cat_path                       varchar(100)                   default ',',
    
   child_count                    mediumint unsigned             default 0,
   primary key (virtual_cat_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_goods_virtual_cat
(
   disabled
)
/*tbindex
 array('sdb_goods_virtual_cat'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_p_order                                           */
/*==============================================================*/
create index ind_p_order on sdb_goods_virtual_cat
(
   p_order
)
/*tbindex
 array('sdb_goods_virtual_cat'=>
  array(
      'name'=>"ind_p_order", 'colum'=>array("p_order")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_cat_path                                          */
/*==============================================================*/
create index ind_cat_path on sdb_goods_virtual_cat
(
   cat_path
)
/*tbindex
 array('sdb_goods_virtual_cat'=>
  array(
      'name'=>"ind_cat_path", 'colum'=>array("cat_path")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_link                                              */
/*==============================================================*/
create table sdb_link
(
    
   link_id                        mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   link_name                      varchar(128),
    
   href                           varchar(255),
    
   image_url                      varchar(255),
    
   orderlist                      mediumint(8),
    
   disabled                       enum('true','false')           default 'false',
   primary key (link_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_lnk_acts                                          */
/*==============================================================*/
create table sdb_lnk_acts
(
    
   role_id                        int unsigned                   not null,
    
   action_id                      int unsigned                   not null,
   primary key (role_id, action_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_lnk_roles                                         */
/*==============================================================*/
create table sdb_lnk_roles
(
    
   op_id                          mediumint(8) unsigned          not null,
    
   role_id                        int unsigned                   not null,
   primary key (op_id, role_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_logs                                              */
/*==============================================================*/
create table sdb_logs
(
    
   log_id                         bigint                         not null AUTO_INCREMENT,
    
   member_id                      mediumint unsigned,
    
   goods_id                       mediumint unsigned,
    
   op_id                          mediumint(8) unsigned,
    
   log_obj                        enum('advance','score')        not null default 'score',
    
   logforman                      longtext,
    
   logforcmp                      longtext,
    
   date_line                      int(10)                        not null default 0,
   primary key (log_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_member_addrs                                      */
/*==============================================================*/
create table sdb_member_addrs
(
    
   addr_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   member_id                      mediumint unsigned             not null default 0,
    
   name                           varchar(50),
    
   area                           varchar(255),
    
   country                        varchar(30),
    
   province                       varchar(30),
    
   city                           varchar(50),
    
   addr                           varchar(255),
    
   zip                            varchar(20),
    
   tel                            varchar(30),
    
   mobile                         varchar(30),
    
   def_addr                       tinyint(1)                     default 0,
   primary key (addr_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_member_attr                                       */
/*==============================================================*/
create table sdb_member_attr
(
    
   attr_id                        int unsigned                   not null AUTO_INCREMENT,
    
   attr_name                      varchar(20)                    not null default '',
    
   attr_type                      varchar(20)                    not null default '',
    
   attr_required                  enum('true','false')           not null default 'false',
    
   attr_search                    enum('true','false')           not null default 'false',
    
   attr_option                    text,
    
   attr_valtype                   varchar(20)                    not null default '',
    
   disabled                       enum('true','false')           not null default 'false',
    
   attr_tyname                    varchar(20)                    not null default '',
    
   attr_group                     varchar(20)                    not null default '',
    
   attr_show                      enum('true','false')           not null default 'true',
    
   attr_order                     int unsigned                   not null default 0,
   primary key (attr_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_member_coupon                                     */
/*==============================================================*/
create table sdb_member_coupon
(
    
   memc_code                      varchar(255)                   not null,
    
   cpns_id                        mediumint unsigned             not null,
    
   member_id                      mediumint unsigned             not null,
    
   memc_gen_orderid               varchar(15),
    
   memc_source                    enum('a','b','c')              not null default 'a',
    
   memc_enabled                   enum('true','false')           not null default 'true',
    
   memc_used_times                mediumint                      default 0,
    
   memc_gen_time                  int(10),
   primary key (memc_code)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_member_dealer                                     */
/*==============================================================*/
create table sdb_member_dealer
(
    
   member_id                      mediumint (8)                  not null AUTO_INCREMENT,
    
   dealer_site                    varchar(200)                   not null,
    
   dealer_site_name               varchar(200)                   not null,
    
   dealer_logo                    varchar(200)                   not null,
    
   dealer_consignee               varchar(200)                   not null,
    
   dealer_phone                   varchar(200)                   not null,
    
   dealer_mobile                  varchar(200)                   not null,
    
   dealer_area                    varchar(255)                   not null,
    
   dealer_add                     varchar(255)                   not null,
    
   dealer_zip                     varchar(20)                    not null,
    
   dealer_email                   varchar(255)                   not null,
   primary key (member_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_member_lv                                         */
/*==============================================================*/
create table sdb_member_lv
(
    
   member_lv_id                   mediumint unsigned             not null AUTO_INCREMENT,
    
   name                           varchar(100)                   not null,
    
   dis_count                      decimal(5,2)                   not null default 1,
    
   pre_id                         mediumint,
    
   default_lv                     tinyint(1)                     not null default 0,
    
   deposit_freeze_time            int                            default 0,
    
   deposit                        int                            default 0,
    
   more_point                     int                            default 1,
    
   point                          mediumint(8)                   not null default 0,
    
   lv_type                        enum('retail', 'wholesale','dealer') not null default 'retail',
    
   disabled                       enum('true','false')           default 'false',
    
   show_other_price               enum('true','false')           not null default 'true',
    
   order_limit                    tinyint(1)                     not null default 0,
    
   order_limit_price              decimal(20,3)                  not null default 0.000,
    
   lv_remark                      text,
   primary key (member_lv_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_member_lv
(
   disabled
)
/*tbindex
 array('sdb_member_lv'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_member_mattrvalue                                 */
/*==============================================================*/
create table sdb_member_mattrvalue
(
    
   attr_id                        int unsigned                   not null default 0,
    
   member_id                      mediumint unsigned             not null default 0,
    
   value                          varchar(100)                   not null default '',
    
   id                             int unsigned                   not null AUTO_INCREMENT,
   primary key (id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_members                                           */
/*==============================================================*/
create table sdb_members
(
    
   member_id                      mediumint unsigned             not null AUTO_INCREMENT,
    
   member_lv_id                   mediumint                      not null,
    
   uname                          varchar(50),
    
   name                           varchar(50),
    
   lastname                       varchar(50),
    
   firstname                      varchar(50),
    
   password                       varchar(32),
    
   area                           varchar(255),
    
   mobile                         varchar(30),
    
   tel                            varchar(30),
    
   email                          varchar(200),
    
   zip                            varchar(20),
    
   addr                           varchar(255),
    
   province                       varchar(20),
    
   city                           varchar(20),
    
   order_num                      mediumint unsigned             default 0,
    
   refer_id                       varchar(50),
    
   refer_url                      varchar(200),
    
   b_year                         smallint unsigned,
    
   b_month                        tinyint unsigned,
    
   b_day                          tinyint unsigned,
    
   sex                            enum('0','1')                  not null default '1',
    
   addon                          longtext,
    
   wedlock                        enum('0','1')                  not null default '0',
    
   education                      varchar(30),
    
   vocation                       varchar(50),
    
   interest                       longtext,
    
   advance                        decimal(20,3)                  not null default 0.00,
    
   advance_freeze                 decimal(20,3)                  not null default 0.00,
    
   point_freeze                   mediumint unsigned             not null default 0,
    
   point_history                  mediumint unsigned             not null default 0,
    
   point                          mediumint unsigned             not null default 0,
    
   score_rate                     decimal(5,3),
    
   reg_ip                         varchar(16),
    
   regtime                        integer unsigned,
    
   state                          tinyint(1)                     not null default 0,
    
   pay_time                       mediumint unsigned,
    
   biz_money                      decimal(20,3)                  not null default 0,
    
   pw_answer                      varchar(250),
    
   pw_question                    varchar(250),
    
   fav_tags                       longtext,
    
   custom                         longtext,
    
   cur                            varchar(20),
    
   lang                           varchar(20),
    
   unreadmsg                      smallint unsigned              not null default 0,
    
   disabled                       enum('true','false')           default 'false',
    
   remark                         text,
    
   role_type                      enum('wholesale','dealer')     not null default 'wholesale',
    
   remark_type                    varchar(2)                     not null default 'b1',
   primary key (member_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_email                                             */
/*==============================================================*/
create index ind_email on sdb_members
(
   email
)
/*tbindex
 array('sdb_members'=>
  array(
      'name'=>"ind_email", 'colum'=>array("email")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: uni_user                                              */
/*==============================================================*/
create unique index uni_user on sdb_members
(
   uname
)
/*tbindex
 array('sdb_members'=>
  array(
      'name'=>"uni_user", 'colum'=>array("uname")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_regtime                                           */
/*==============================================================*/
create index ind_regtime on sdb_members
(
   regtime
)
/*tbindex
 array('sdb_members'=>
  array(
      'name'=>"ind_regtime", 'colum'=>array("regtime")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_members
(
   disabled
)
/*tbindex
 array('sdb_members'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_message                                           */
/*==============================================================*/
create table sdb_message
(
    
   msg_id                         mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   for_id                         mediumint(8) unsigned          not null default 0,
    
   msg_from                       varchar(30)                    not null default 'anonymous',
    
   from_id                        mediumint unsigned             default 0,
    
   from_type                      tinyint(1) unsigned            not null default 0,
    
   to_id                          mediumint(8) unsigned          not null default 0,
    
   to_type                        tinyint(1) unsigned            not null default 0,
    
   unread                         enum('1','0')                  not null default '0',
    
   folder                         enum('inbox','outbox')         not null default 'inbox',
    
   email                          varchar(255),
    
   tel                            varchar(30),
    
   subject                        varchar(100)                   not null,
    
   message                        longtext                       not null,
    
   rel_order                      bigint unsigned                default 0,
    
   date_line                      int(10)                        not null default 0,
    
   is_sec                         enum('true','false')           not null default 'true',
    
   del_status                     enum('0','1','2')              default '0',
    
   disabled                       enum('true','false')           not null default 'false',
    
   msg_ip                         varchar(20)                    not null default '',
    
   msg_type                       enum('default','payment')      not null default 'default',
   primary key (msg_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_to_id                                             */
/*==============================================================*/
create index ind_to_id on sdb_message
(
   to_id,
   folder,
   from_type,
   unread
)
/*tbindex
 array('sdb_message'=>
  array(
      'name'=>"ind_to_id", 'colum'=>array("to_id,
folder,
from_type,
unread")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_from_id                                           */
/*==============================================================*/
create index ind_from_id on sdb_message
(
   from_id,
   folder,
   to_type
)
/*tbindex
 array('sdb_message'=>
  array(
      'name'=>"ind_from_id", 'colum'=>array("from_id,
folder,
to_type")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_message
(
   disabled
)
/*tbindex
 array('sdb_message'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_msgqueue                                          */
/*==============================================================*/
create table sdb_msgqueue
(
    
   queue_id                       mediumint unsigned             not null AUTO_INCREMENT,
    
   title                          varchar(250),
    
   target                         varchar(250)                   not null,
    
   event_name                     varchar(50),
    
   data                           longtext,
    
   tmpl_name                      varchar(50)                    not null,
    
   level                          tinyint unsigned               not null default 5,
    
   sender                         varchar(50)                    not null,
    
   sender_order                   tinyint unsigned               not null default 5,
   primary key (queue_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_level                                             */
/*==============================================================*/
create index ind_level on sdb_msgqueue
(
   level
)
/*tbindex
 array('sdb_msgqueue'=>
  array(
      'name'=>"ind_level", 'colum'=>array("level")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_op_sessions                                       */
/*==============================================================*/
create table sdb_op_sessions
(
    
   sess_id                        varchar(32)                    not null,
    
   op_id                          mediumint(6) unsigned,
    
   login_time                     int(10),
    
   last_time                      int(10),
    
   pkg                            varchar(50),
    
   ctl                            varchar(100),
    
   act                            varchar(50),
    
   api_id                         mediumint(8) unsigned,
    
   sess_data                      longtext,
    
   status                         tinyint(1)                     default 0,
    
   ip                             varchar(17),
   primary key (sess_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_operators                                         */
/*==============================================================*/
create table sdb_operators
(
    
   op_id                          mediumint(8) unsigned          not null AUTO_INCREMENT,
    
   username                       varchar(20)                    not null,
    
   userpass                       varchar(32)                    not null,
    
   name                           varchar(30),
    
   config                         longtext,
    
   favorite                       longtext,
    
   status                         tinyint(1)                     not null default 1,
    
   super                          tinyint(1)                     not null default 0,
    
   lastip                         varchar(20)                    not null default '',
    
   logincount                     mediumint unsigned             not null default 0,
    
   lastlogin                      integer(10) unsigned           not null default 0,
    
   disabled                       enum('false','true')           not null default 'false',
    
   op_no                          varchar(50)                    not null default '',
    
   department                     varchar(50)                    not null default '',
    
   memo                           text,
   primary key (op_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: uni_username                                          */
/*==============================================================*/
create unique index uni_username on sdb_operators
(
   username
)
/*tbindex
 array('sdb_operators'=>
  array(
      'name'=>"uni_username", 'colum'=>array("username")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_operators
(
   disabled
)
/*tbindex
 array('sdb_operators'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_order_archives                                    */
/*==============================================================*/
create table sdb_order_archives
(
    
   order_id                       bigint unsigned
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_order_items                                       */
/*==============================================================*/
create table sdb_order_items
(
    
   item_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   order_id                       bigint unsigned                not null,
    
   product_id                     mediumint unsigned             not null,
    
   dly_status                     enum('storage','shipping','return','customer','returned') not null default 'storage',
    
   type_id                        mediumint unsigned,
    
   bn                             varchar(30),
    
   name                           varchar(200),
    
   cost                           decimal(20,3),
    
   price                          decimal(20,3)                  not null default 0,
    
   amount                         decimal(20,3),
    
   score                          mediumint unsigned,
    
   nums                           mediumint unsigned             not null default 1,
    
   minfo                          longtext,
    
   sendnum                        mediumint unsigned             not null default 0,
    
   addon                          longtext,
    
   is_type                        enum('goods','pkg')            not null default 'goods',
    
   point                          mediumint,
   primary key (item_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_order_log                                         */
/*==============================================================*/
create table sdb_order_log
(
    
   log_id                         int(10)                        not null AUTO_INCREMENT,
    
   order_id                       bigint(20),
    
   op_id                          mediumint(8),
    
   op_name                        varchar(30),
    
   log_text                       longtext,
    
   acttime                        int(10),
    
   behavior                       varchar(20)                    default '',
    
   result                         enum('success','failure')      default 'success',
   primary key (log_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_order_pmt                                         */
/*==============================================================*/
create table sdb_order_pmt
(
    
   pmt_id                         bigint(20) unsigned            not null,
    
   order_id                       bigint unsigned                not null,
    
   pmt_amount                     decimal(20,3),
    
   pmt_memo                       longtext,
    
   pmt_describe                   longtext,
   primary key (pmt_id, order_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_order_tmpl                                        */
/*==============================================================*/
create table sdb_order_tmpl
(
    
   id                             int unsigned                   not null AUTO_INCREMENT,
    
   name                           varchar(200)                   not null,
    
   content                        longtext,
    
   intro                          longtext,
    
   create_time                    int unsigned,
    
   update_time                    int unsigned,
    
   disabled                       enum('false','true')           not null default 'false',
   primary key (id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_orders                                            */
/*==============================================================*/
create table sdb_orders
(
    
   order_id                       bigint unsigned                not null,
    
   member_id                      mediumint unsigned,
    
   confirm                        enum('Y','N')                  not null default 'N',
    
   status                         enum('active','dead','finish') not null default 'active',
    
   pay_status                     tinyint unsigned               not null default 0,
    
   ship_status                    tinyint unsigned               not null default 0,
    
   user_status                    enum('null','payed','shipped') not null default 'null',
    
   is_delivery                    enum('Y','N')                  not null default 'Y',
    
   shipping_id                    smallint(4) unsigned,
    
   shipping                       varchar(100),
    
   shipping_area                  varchar(50),
    
   payment                        mediumint                      default 0,
    
   weight                         decimal(20,3),
    
   tostr                          longtext,
    
   itemnum                        mediumint unsigned,
    
   acttime                        int,
    
   createtime                     int,
    
   refer_id                       varchar(50),
    
   refer_url                      varchar(200),
    
   ip                             varchar(15),
    
   ship_name                      varchar(50),
    
   ship_area                      varchar(255),
    
   ship_addr                      varchar(100),
    
   ship_zip                       varchar(20),
    
   ship_tel                       varchar(30),
    
   ship_email                     varchar(150),
    
   ship_time                      varchar(50),
    
   ship_mobile                    varchar(50),
    
   cost_item                      decimal(20,3)                  not null default 0,
    
   is_tax                         enum('false','true')           not null default 'false',
    
   cost_tax                       decimal(20,3)                  not null default 0,
    
   tax_company                    varchar(255),
    
   cost_freight                   decimal(20,3)                  not null default 0,
    
   is_protect                     enum('false','true')           not null default 'false',
    
   cost_protect                   decimal(20,3)                  not null default 0,
    
   cost_payment                   decimal(20,3),
    
   currency                       varchar(8),
    
   cur_rate                       decimal(10,4)                  default 1.0000,
    
   score_u                        decimal(20,3)                  not null default 0,
    
   score_g                        decimal(20,3)                  not null default 0,
    
   advance                        decimal(20,3)                  default 0,
    
   discount                       decimal(20,3)                  not null default 0,
    
   use_pmt                        varchar(30),
    
   total_amount                   decimal(20,3)                  not null default 0,
    
   final_amount                   decimal(20,3)                  not null default 0,
    
   pmt_amount                     decimal(20,3),
    
   payed                          decimal(20,3)                  default 0,
    
   markstar                       enum('Y','N')                  default 'N',
    
   memo                           longtext,
    
   print_status                   tinyint unsigned               not null default 0,
    
   mark_text                      longtext,
    
   disabled                       enum('true','false')           default 'false',
    
   last_change_time               int(11)                        not null default 0,
    
   use_registerinfo               enum('true','false')           default 'false',
    
   mark_type                      varchar(2)                     not null default 'b1',
   primary key (order_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_ship_status                                       */
/*==============================================================*/
create index ind_ship_status on sdb_orders
(
   ship_status
)
/*tbindex
 array('sdb_orders'=>
  array(
      'name'=>"ind_ship_status", 'colum'=>array("ship_status")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_pay_status                                        */
/*==============================================================*/
create index ind_pay_status on sdb_orders
(
   pay_status
)
/*tbindex
 array('sdb_orders'=>
  array(
      'name'=>"ind_pay_status", 'colum'=>array("pay_status")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_status                                            */
/*==============================================================*/
create index ind_status on sdb_orders
(
   status
)
/*tbindex
 array('sdb_orders'=>
  array(
      'name'=>"ind_status", 'colum'=>array("status")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_orders
(
   disabled
)
/*tbindex
 array('sdb_orders'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_package                                           */
/*==============================================================*/
create table sdb_package
(
    
   pkg_id                         varchar(100)                   not null,
    
   disabled                       enum('true','false')           not null default 'false',
    
   dbver                          mediumint unsigned,
    
   adminschema                    longtext,
    
   shopaction                     longtext,
    
   installed                      enum('true','false')           not null default 'false',
   primary key (pkg_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_package
(
   disabled
)
/*tbindex
 array('sdb_package'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_package_product                                   */
/*==============================================================*/
create table sdb_package_product
(
    
   product_id                     mediumint unsigned             not null,
    
   goods_id                       mediumint unsigned             not null,
    
   discount                       decimal(5,3),
    
   pkgnum                         mediumint unsigned             not null default 1,
   primary key (product_id, goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pages                                             */
/*==============================================================*/
create table sdb_pages
(
    
   page_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   page_name                      varchar(90)                    not null,
    
   page_title                     varchar(90)                    not null,
    
   page_content                   longtext,
    
   page_time                      int unsigned                   not null,
   primary key (page_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: uni_pagename                                          */
/*==============================================================*/
create unique index uni_pagename on sdb_pages
(
   page_name
)
/*tbindex
 array('sdb_pages'=>
  array(
      'name'=>"uni_pagename", 'colum'=>array("page_name")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: uni_pagetitle                                         */
/*==============================================================*/
create unique index uni_pagetitle on sdb_pages
(
   page_title
)
/*tbindex
 array('sdb_pages'=>
  array(
      'name'=>"uni_pagetitle", 'colum'=>array("page_title")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_passport_cfg                                      */
/*==============================================================*/
create table sdb_passport_cfg
(
    
   passport_type                  varchar(30)                    not null,
    
   config                         longtext,
    
   order_num                      smallint(3) unsigned           not null default 0,
    
   disabled                       enum('true','false')           default 'false',
    
   ifvalid                        enum('true','false')           default 'false',
   primary key (passport_type)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_payment_cfg                                       */
/*==============================================================*/
create table sdb_payment_cfg
(
    
   id                             mediumint unsigned             not null AUTO_INCREMENT,
    
   custom_name                    varchar(100),
    
   pay_type                       varchar(30)                    not null,
    
   config                         longtext,
    
   fee                            decimal(9,5)                   not null default 0,
    
   des                            longtext,
    
   order_num                      smallint(3) unsigned           not null default 0,
    
   disabled                       enum('true','false')           default 'false',
    
   orderlist                      mediumint(8) unsigned,
   primary key (id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_payments                                          */
/*==============================================================*/
create table sdb_payments
(
    
   payment_id                     varchar(20)                    not null,
    
   order_id                       bigint unsigned,
    
   member_id                      mediumint unsigned,
    
   account                        varchar(50),
    
   bank                           varchar(50),
    
   pay_account                    varchar(50),
    
   currency                       varchar(10),
    
   money                          decimal(20,3)                  not null default 0,
    
   paycost                        decimal(20,3),
    
   cur_money                      decimal(20,3)                  not null default 0,
    
   pay_type                       enum('online','offline','deposit','recharge','joinfee') not null default 'online',
    
   payment                        mediumint unsigned             not null,
    
   paymethod                      varchar(100),
    
   op_id                          mediumint(8) unsigned,
    
   ip                             varchar(20),
    
   t_begin                        int(10),
    
   t_end                          int(10),
    
   status                         enum('succ','failed','cancel','error','progress','invalid','timeout','ready') not null default 'ready',
    
   memo                           longtext,
    
   disabled                       enum('true','false')           default 'false',
    
   trade_no                       varchar(30),
   primary key (payment_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_payments
(
   disabled
)
/*tbindex
 array('sdb_payments'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_pdt_actions                                       */
/*==============================================================*/
create table sdb_pdt_actions
(
    
   action_id                      mediumint unsigned             not null AUTO_INCREMENT,
    
   product_id                     mediumint unsigned             not null,
    
   member_id                      mediumint unsigned             not null,
    
   type                           tinyint unsigned               not null,
    
   money                          decimal(20,3)                  not null,
   primary key (action_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pmt_gen_coupon                                    */
/*==============================================================*/
create table sdb_pmt_gen_coupon
(
    
   pmt_id                         mediumint unsigned             not null,
    
   cpns_id                        mediumint unsigned             not null,
    
   disabled                       enum('true','false')           default 'false',
   primary key (pmt_id, cpns_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pmt_goods                                         */
/*==============================================================*/
create table sdb_pmt_goods
(
    
   pmt_id                         mediumint unsigned             not null,
    
   count                          mediumint unsigned             default 0,
    
   goods_id                       mediumint unsigned             not null,
   primary key (pmt_id, goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pmt_goods_cat                                     */
/*==============================================================*/
create table sdb_pmt_goods_cat
(
    
   cat_id                         mediumint unsigned             not null default 0,
    
   brand_id                       mediumint unsigned             not null default 0,
    
   pmt_id                         mediumint unsigned             not null,
   primary key (cat_id, brand_id, pmt_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pmt_member_lv                                     */
/*==============================================================*/
create table sdb_pmt_member_lv
(
    
   member_lv_id                   mediumint,
    
   pmt_id                         mediumint unsigned
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_point_history                                     */
/*==============================================================*/
create table sdb_point_history
(
    
   id                             mediumint unsigned             not null AUTO_INCREMENT,
    
   member_id                      mediumint                      not null,
    
   point                          int(10)                        not null,
    
   time                           int(10)                        not null,
    
   reason                         varchar(50)                    not null,
    
   related_id                     bigint unsigned,
    
   type                           tinyint(1)                     not null,
    
   operator                       varchar(50),
   primary key (id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_print_tmpl                                        */
/*==============================================================*/
create table sdb_print_tmpl
(
    
   prt_tmpl_id                    int unsigned                   not null AUTO_INCREMENT,
    
   prt_tmpl_title                 varchar(100)                   not null,
    
   shortcut                       enum('false','true')           default 'false',
    
   disabled                       enum('false','true')           default 'false',
    
   prt_tmpl_width                 tinyint unsigned               not null default 100,
    
   prt_tmpl_height                tinyint unsigned               not null default 100,
    
   prt_tmpl_data                  longtext,
   primary key (prt_tmpl_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_product_memo                                      */
/*==============================================================*/
create table sdb_product_memo
(
    
   product_id                     mediumint unsigned             not null,
    
   p_key                          varchar(20)                    not null,
    
   p_value                        longtext,
   primary key (product_id, p_key)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_products                                          */
/*==============================================================*/
create table sdb_products
(
    
   product_id                     mediumint unsigned             not null AUTO_INCREMENT,
    
   goods_id                       mediumint unsigned             not null default 0,
    
   barcode                        varchar(128),
    
   title                          varchar(255),
    
   bn                             varchar(30),
    
   price                          decimal(20,3)                  not null default 0,
    
   cost                           decimal(20,3)                  default 0,
    
   name                           varchar(200)                   not null,
    
   weight                         decimal(20,3),
    
   unit                           varchar(20),
    
   store                          mediumint unsigned,
    
   freez                          mediumint unsigned,
    
   pdt_desc                       longtext,
    
   props                          longtext,
    
   uptime                         int(10),
    
   last_modify                    int(10),
    
   disabled                       enum('true','false')           default 'false',
   primary key (product_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_products
(
   disabled
)
/*tbindex
 array('sdb_products'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_promotion                                         */
/*==============================================================*/
create table sdb_promotion
(
    
   pmt_id                         mediumint unsigned             not null AUTO_INCREMENT,
    
   pmts_id                        varchar(255)                   not null,
    
   pmta_id                        mediumint unsigned,
    
   pmt_time_begin                 int(10),
    
   pmt_time_end                   int(10),
    
   order_money_from               decimal(20,3)                  not null default 0,
    
   order_money_to                 decimal(20,3)                  not null default 9999999,
    
   seq                            tinyint unsigned               not null default 0,
    
   pmt_type                       enum('0','1','2')              not null default '0',
    
   pmt_belong                     enum('0','1')                  not null default '0',
    
   pmt_bond_type                  enum('0','1','2')              not null,
    
   pmt_describe                   longtext,
    
   pmt_solution                   longtext,
    
   pmt_ifcoupon                   tinyint unsigned               not null default 1,
    
   pmt_update_time                int(10)                        default 0,
    
   pmt_basic_type                 enum('goods','order')          default 'goods',
    
   disabled                       enum('true','false')           default 'false',
    
   pmt_ifsale                     enum('true','false')           not null default 'true',
    
   pmt_distype                    tinyint unsigned               not null default 0,
   primary key (pmt_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_promotion
(
   disabled
)
/*tbindex
 array('sdb_promotion'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_promotion_activity                                */
/*==============================================================*/
create table sdb_promotion_activity
(
    
   pmta_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   pmta_name                      varchar(200),
    
   pmta_enabled                   enum('true','false'),
    
   pmta_time_begin                int(10),
    
   pmta_time_end                  int(10),
    
   pmta_describe                  longtext,
    
   disabled                       enum('true','false')           default 'false',
   primary key (pmta_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_promotion_activity
(
   disabled
)
/*tbindex
 array('sdb_promotion_activity'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_promotion_scheme                                  */
/*==============================================================*/
create table sdb_promotion_scheme
(
    
   pmts_id                        mediumint unsigned             not null,
    
   pmts_name                      varchar(250),
    
   pmts_memo                      longtext,
    
   pmts_solution                  longtext,
    
   pmts_type                      tinyint(3)                     not null,
   primary key (pmts_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_pub_files                                         */
/*==============================================================*/
create table sdb_pub_files
(
    
   file_id                        int                            not null AUTO_INCREMENT,
    
   file_name                      varchar(50),
    
   file_ident                     varchar(100)                   not null,
    
   cdate                          int unsigned                   not null,
    
   memo                           varchar(250),
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (file_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_pub_files
(
   disabled
)
/*tbindex
 array('sdb_pub_files'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_refunds                                           */
/*==============================================================*/
create table sdb_refunds
(
    
   refund_id                      bigint unsigned                not null AUTO_INCREMENT,
    
   order_id                       bigint unsigned,
    
   member_id                      mediumint unsigned,
    
   account                        varchar(50),
    
   bank                           varchar(50),
    
   pay_account                    varchar(250),
    
   currency                       varchar(20),
    
   money                          decimal(20,3)                  not null default 0,
    
   pay_type                       enum('online','offline','deposit') default 'offline',
    
   payment                        mediumint unsigned             not null,
    
   paymethod                      varchar(100),
    
   ip                             varchar(20),
    
   t_ready                        int unsigned                   not null,
    
   t_sent                         int unsigned,
    
   t_received                     int unsigned,
    
   status                         enum('ready','progress','sent','received','cancel') not null default 'ready',
    
   memo                           longtext,
    
   title                          varchar(255)                   not null,
    
   send_op_id                     mediumint unsigned,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (refund_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_refunds
(
   disabled
)
/*tbindex
 array('sdb_refunds'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_regions                                           */
/*==============================================================*/
create table sdb_regions
(
    
   region_id                      int unsigned                   not null AUTO_INCREMENT,
    
   package                        varchar(20)                    not null,
    
   p_region_id                    int unsigned,
    
   region_path                    varchar(255),
    
   region_grade                   mediumint(8) unsigned,
    
   local_name                     varchar(50)                    not null,
    
   en_name                        varchar(50),
    
   p_1                            varchar(50),
    
   p_2                            varchar(50),
    
   ordernum                       mediumint(8) unsigned,
    
   disabled                       enum('true','false')           default 'false',
   primary key (region_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_return_product                                    */
/*==============================================================*/
create table sdb_return_product
(
    
   order_id                       bigint unsigned                not null default 0,
    
   member_id                      mediumint unsigned             not null default 0,
    
   return_id                      bigint unsigned                not null AUTO_INCREMENT,
    
   title                          varchar(200)                   not null default '',
    
   content                        longtext,
    
   status                         int unsigned                   not null default 1,
    
   image_file                     varchar(255)                   not null default '',
    
   product_data                   longtext,
    
   comment                        longtext,
    
   add_time                       int                            not null default 0,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (return_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_sell_logs                                         */
/*==============================================================*/
create table sdb_sell_logs
(
    
   log_id                         mediumint(8)                   not null AUTO_INCREMENT,
    
   member_id                      mediumint(8)                   not null default 0,
    
   name                           varchar(50)                    default '',
    
   price                          decimal(20,3)                  default 0,
    
   product_id                     mediumint(8)                   not null default 0,
    
   goods_id                       mediumint unsigned             not null,
    
   product_name                   varchar(200)                   default '',
    
   pdt_desc                       varchar(200)                   default '',
    
   number                         int(10)                        default 0,
    
   createtime                     int(10),
   primary key (log_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: idx_goods_id                                          */
/*==============================================================*/
create index idx_goods_id on sdb_sell_logs
(
   member_id,
   product_id,
   goods_id
)
/*tbindex
 array('sdb_sell_logs'=>
  array(
      'name'=>"idx_goods_id", 'colum'=>array("member_id,
product_id,
goods_id")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_sendbox                                           */
/*==============================================================*/
create table sdb_sendbox
(
    
   out_id                         int                            not null AUTO_INCREMENT,
    
   tmpl_name                      varchar(50),
    
   sender                         varchar(50)                    not null,
    
   creattime                      int unsigned                   not null,
    
   target                         longtext,
    
   sendcount                      mediumint unsigned,
    
   content                        varchar(200),
    
   subject                        varchar(100),
   primary key (out_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_sender                                            */
/*==============================================================*/
create index ind_sender on sdb_sendbox
(
   sender
)
/*tbindex
 array('sdb_sendbox'=>
  array(
      'name'=>"ind_sender", 'colum'=>array("sender")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_settings                                          */
/*==============================================================*/
create table sdb_settings
(
    
   s_name                         varchar(16)                    not null,
    
   s_data                         longtext,
    
   s_time                         int(10)                        not null,
    
   disabled                       enum('true','false')           default 'false',
   primary key (s_name)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_sfiles                                            */
/*==============================================================*/
create table sdb_sfiles
(
    
   file_id                        varchar(32)                    not null,
    
   file_name                      varchar(32)                    not null,
    
   usedby                         varchar(32),
    
   file_type                      varchar(32),
    
   file_size                      int(9)                         not null,
    
   cdate                          int(10)                        not null,
    
   misc                           varchar(255),
   primary key (file_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_usedby                                            */
/*==============================================================*/
create index ind_usedby on sdb_sfiles
(
   usedby
)
/*tbindex
 array('sdb_sfiles'=>
  array(
      'name'=>"ind_usedby", 'colum'=>array("usedby")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_sitemaps                                          */
/*==============================================================*/
create table sdb_sitemaps
(
    
   node_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   p_node_id                      mediumint unsigned             not null default 0,
    
   node_type                      varchar(30)                    not null,
    
   depth                          tinyint unsigned               not null,
    
   path                           varchar(200),
    
   title                          varchar(100)                   not null,
    
   action                         varchar(100)                   not null,
    
   manual                         enum('0','1')                  not null default '1',
    
   item_id                        mediumint unsigned,
    
   p_order                        mediumint unsigned,
    
   hidden                         enum('true','false')           not null default 'false',
    
   child_count                    mediumint(4),
   primary key (node_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_hidden                                            */
/*==============================================================*/
create index ind_hidden on sdb_sitemaps
(
   hidden
)
/*tbindex
 array('sdb_sitemaps'=>
  array(
      'name'=>"ind_hidden", 'colum'=>array("hidden")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_spec_values                                       */
/*==============================================================*/
create table sdb_spec_values
(
    
   spec_value_id                  mediumint unsigned             not null AUTO_INCREMENT,
    
   spec_id                        mediumint unsigned             not null default 0,
    
   spec_value                     varchar(100)                   not null default '',
    
   spec_image                     varchar(255)                   not null default '',
    
   p_order                        mediumint unsigned             not null default 50,
   primary key (spec_value_id, spec_value)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_specification                                     */
/*==============================================================*/
create table sdb_specification
(
    
   spec_id                        mediumint unsigned             not null AUTO_INCREMENT,
    
   spec_name                      varchar(50)                    not null default '',
    
   spec_show_type                 enum('select','flat')          not null default 'flat',
    
   spec_type                      enum('text','image')           not null default 'text',
    
   spec_memo                      varchar(50)                    not null default '',
    
   p_order                        mediumint unsigned             not null default 0,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (spec_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_status                                            */
/*==============================================================*/
create table sdb_status
(
    
   status_key                     varchar(20)                    not null,
    
   date_affect                    date                           not null default '0000-00-00',
    
   status_value                   varchar(100)                   not null default '0',
    
   last_update                    int unsigned                   not null,
   primary key (status_key, date_affect)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_supplier_goods_delete                             */
/*==============================================================*/
create table sdb_supplier_goods_delete
(
    
   goods_id                       mediumint unsigned             not null,
    
   sync_status                    enum('0','1')                  default '0',
    
   goods_name                     varchar(255),
   primary key (goods_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_supplier_sync                                     */
/*==============================================================*/
create table sdb_supplier_sync
(
    
   supplier_id                    int                            not null,
    
   last_time                      int,
   primary key (supplier_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_systmpl                                           */
/*==============================================================*/
create table sdb_systmpl
(
    
   tmpl_name                      varchar(50)                    not null,
    
   content                        longtext,
    
   edittime                       int unsigned                   not null default 0,
    
   active                         enum('true','false')           not null default 'true',
   primary key (tmpl_name)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_tag_rel                                           */
/*==============================================================*/
create table sdb_tag_rel
(
    
   tag_id                         mediumint unsigned             not null,
    
   rel_id                         bigint unsigned                not null,
   primary key (tag_id, rel_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_tags                                              */
/*==============================================================*/
create table sdb_tags
(
    
   tag_id                         mediumint unsigned             not null AUTO_INCREMENT,
    
   tag_name                       varchar(20)                    not null,
    
   tag_type                       varchar(20)                    not null,
    
   rel_count                      mediumint unsigned             not null default 0,
   primary key (tag_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_type                                              */
/*==============================================================*/
create index ind_type on sdb_tags
(
   tag_type
)
/*tbindex
 array('sdb_tags'=>
  array(
      'name'=>"ind_type", 'colum'=>array("tag_type")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_name                                              */
/*==============================================================*/
create index ind_name on sdb_tags
(
   tag_name
)
/*tbindex
 array('sdb_tags'=>
  array(
      'name'=>"ind_name", 'colum'=>array("tag_name")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_themes                                            */
/*==============================================================*/
create table sdb_themes
(
    
   theme                          varchar(50)                    not null,
    
   name                           varchar(50),
    
   stime                          int unsigned,
    
   author                         varchar(50),
    
   site                           varchar(100),
    
   version                        varchar(50),
    
   info                           varchar(255),
    
   config                         longtext,
    
   update_url                     varchar(100),
    
   template                       varchar(255),
   primary key (theme)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_type_brand                                        */
/*==============================================================*/
create table sdb_type_brand
(
    
   type_id                        mediumint unsigned             not null,
    
   brand_id                       mediumint unsigned             not null,
    
   brand_order                    mediumint unsigned,
   primary key (type_id, brand_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_wholesale                                         */
/*==============================================================*/
create table sdb_wholesale
(
    
   ws_id                          mediumint unsigned             not null AUTO_INCREMENT,
    
   ws_no                          varchar(100)                   not null,
    
   ws_name                        varchar(200),
    
   ws_btime                       int(10),
    
   ws_etime                       int(10),
    
   ws_enable                      enum('true','false')           not null default 'true',
    
   ws_belong                      enum('0','1')                  not null default '0',
    
   ws_bind                        tinyint unsigned               not null default 0,
    
   ws_params                      longtext,
    
   ws_object                      enum('goods','order')          not null default 'order',
    
   ws_type                        varchar(50)                    not null,
    
   ws_desc                        longtext,
    
   ws_update_time                 int(10)                        default 0,
    
   ws_order                       int unsigned                   not null default 0,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (ws_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_disabled                                          */
/*==============================================================*/
create index ind_disabled on sdb_wholesale
(
   disabled
)
/*tbindex
 array('sdb_wholesale'=>
  array(
      'name'=>"ind_disabled", 'colum'=>array("disabled")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_wholesale_single                                  */
/*==============================================================*/
create table sdb_wholesale_single
(
    
   wss_id                         mediumint                      not null AUTO_INCREMENT,
    
   wss_name                       varchar(255),
    
   wss_params                     longtext,
    
   wss_update_time                int,
    
   disabled                       enum('true','false')           not null default 'false',
   primary key (wss_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_widgets_set                                       */
/*==============================================================*/
create table sdb_widgets_set
(
    
   widgets_id                     int                            not null AUTO_INCREMENT,
    
   base_file                      varchar(50)                    not null,
    
   base_slot                      tinyint unsigned               not null default 0,
    
   base_id                        varchar(20),
    
   widgets_type                   varchar(20)                    not null,
    
   widgets_order                  tinyint unsigned               not null default 5,
    
   title                          varchar(100),
    
   domid                          varchar(100),
    
   border                         varchar(100),
    
   classname                      varchar(100),
    
   tpl                            varchar(100),
    
   params                         longtext,
    
   modified                       int(10),
    
   vary                           varchar(250),
   primary key (widgets_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Index: ind_wgbase                                            */
/*==============================================================*/
create index ind_wgbase on sdb_widgets_set
(
   base_file,
   base_id,
   widgets_order
)
/*tbindex
 array('sdb_widgets_set'=>
  array(
      'name'=>"ind_wgbase", 'colum'=>array("base_file,
base_id,
widgets_order")
  )     
),
tbindex*/;

/*==============================================================*/
/* Index: ind_wginfo                                            */
/*==============================================================*/
create index ind_wginfo on sdb_widgets_set
(
   base_file,
   base_slot,
   widgets_order
)
/*tbindex
 array('sdb_widgets_set'=>
  array(
      'name'=>"ind_wginfo", 'colum'=>array("base_file,
base_slot,
widgets_order")
  )     
),
tbindex*/;

/*==============================================================*/
/* Table: sdb_ws_goods                                          */
/*==============================================================*/
create table sdb_ws_goods
(
    
   ws_id                          mediumint unsigned             not null,
    
   goods_id                       mediumint unsigned             not null default 0,
   primary key (goods_id, ws_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;

/*==============================================================*/
/* Table: sdb_ws_goods_cat                                      */
/*==============================================================*/
create table sdb_ws_goods_cat
(
    
   ws_id                          mediumint unsigned             not null,
    
   cat_id                         mediumint unsigned             not null default 0,
    
   brand_id                       mediumint unsigned             not null default 0,
   primary key (cat_id, brand_id, ws_id)
)
type = MyISAM DEFAULT CHARACTER SET utf8;


create index fk_m_adv_logs on sdb_advance_logs
(
   member_id
);


create index fk_article on sdb_articles
(
   node_id
);


create index fk_comment on sdb_comments
(
   for_comment_id
);


create index fk_p_c on sdb_coupons
(
   pmt_id
);


create index fk_coupons_items on sdb_coupons_p_items
(
   order_id
);


create index fk_o_c_u_i on sdb_coupons_u_items
(
   order_id
);


create index fk_member_dly on sdb_delivery
(
   member_id
);


create index fk_order_dly on sdb_delivery
(
   order_id
);


create index fk_delivery_item on sdb_delivery_item
(
   delivery_id
);


create index fk_dlya_type on sdb_dly_h_area
(
   dt_id
);


create index fk_dlya_area on sdb_dly_h_area
(
   area_id
);


create index fk_gift_cat on sdb_gift
(
   giftcat_id
);


create index fk_order_items on sdb_gift_items
(
   order_id
);


create index fk_fk_gimages on sdb_gimages
(
   goods_id
);


create index fk_m_gnotify on sdb_gnotify
(
   member_id
);


create index fk_g_gnotify on sdb_gnotify
(
   goods_id
);


create index fk_pdt_gnotify on sdb_gnotify
(
   product_id
);


create index fk_type_b_g on sdb_goods
(
   type_id, brand_id
);


create index fk_g_type on sdb_goods
(
   type_id
);


create index fk_goods_cat on sdb_goods
(
   cat_id
);


create index fk_goods_cat on sdb_goods_cat
(
   parent_id
);


create index fk_type_id on sdb_goods_cat
(
   type_id
);


create index fk_idx_goods_keywords on sdb_goods_keywords
(
   goods_id
);


create index fk_pdt_lv_prcie on sdb_goods_lv_price
(
   product_id
);


create index fk_m_lv_price on sdb_goods_lv_price
(
   level_id
);


create index fk_g_lv_price on sdb_goods_lv_price
(
   goods_id
);


create index fk_goods_memo on sdb_goods_memo
(
   goods_id
);


create index fk_goods_id_1 on sdb_goods_rate
(
   goods_1
);


create index fk_goods_id_2 on sdb_goods_rate
(
   goods_2
);


create index fk_spec_goods_index on sdb_goods_spec_index
(
   goods_id
);


create index fk_spec_index on sdb_goods_spec_index
(
   spec_id
);


create index fk_spec_products on sdb_goods_spec_index
(
   product_id
);


create index fk_spec_type_index on sdb_goods_spec_index
(
   type_id
);


create index fk_spec_value_index on sdb_goods_spec_index
(
   spec_value_id, spec_value
);


create index fk_spec_type on sdb_goods_type_spec
(
   spec_id
);


create index fk_type_spec on sdb_goods_type_spec
(
   type_id
);


create index fk_reference_11 on sdb_lnk_acts
(
   role_id
);


create index fk_reference_10 on sdb_lnk_roles
(
   role_id
);


create index fk_reference_8 on sdb_lnk_roles
(
   op_id
);


create index fk_member_logs on sdb_logs
(
   member_id
);


create index fk_goods_logs on sdb_logs
(
   goods_id
);


create index fk_o_logs on sdb_logs
(
   op_id
);


create index fk_member_addr on sdb_member_addrs
(
   member_id
);


create index fk_m_coupons on sdb_member_coupon
(
   cpns_id
);


create index fk_reference_12 on sdb_member_mattrvalue
(
   attr_id
);


create index fk_reference_54 on sdb_member_mattrvalue
(
   member_id
);


create index fk_members on sdb_members
(
   member_lv_id
);


create index fk_m_msg on sdb_message
(
   from_id
);


create index fk_order_msg on sdb_message
(
   rel_order
);


create index fk_orders_items on sdb_order_items
(
   order_id
);


create index fk_order_pdt on sdb_order_items
(
   product_id
);


create index fk_order_log on sdb_order_log
(
   order_id
);


create index fk_order_pmt on sdb_order_pmt
(
   order_id
);


create index fk_members_orders on sdb_orders
(
   member_id
);


create index fk_goods_pkg on sdb_package_product
(
   goods_id
);


create index fk_pdt_pkg on sdb_package_product
(
   product_id
);


create index fk_order_payments on sdb_payments
(
   order_id
);


create index fk_payment_opt on sdb_payments
(
   op_id
);


create index fk_cfg_payments on sdb_payments
(
   payment
);


create index fk_payments on sdb_payments
(
   member_id
);


create index fk_pdt_actions on sdb_pdt_actions
(
   product_id
);


create index fk_mem_pdt_act on sdb_pdt_actions
(
   member_id
);


create index fk_pmt_g_c on sdb_pmt_gen_coupon
(
   pmt_id
);


create index fk_c_p_g on sdb_pmt_gen_coupon
(
   cpns_id
);


create index fk_pdt_pmt on sdb_pmt_goods
(
   goods_id
);


create index fk_pmt_goods on sdb_pmt_goods
(
   pmt_id
);


create index fk_pmt_g_cat on sdb_pmt_goods_cat
(
   pmt_id
);


create index fk_goods_ca_pmtt on sdb_pmt_goods_cat
(
   cat_id
);


create index fk_brd_p_gcat on sdb_pmt_goods_cat
(
   brand_id
);


create index fk_pmt_m_lv on sdb_pmt_member_lv
(
   pmt_id
);


create index fk_m_lv_pmt on sdb_pmt_member_lv
(
   member_lv_id
);


create index fk_product_id on sdb_product_memo
(
   product_id
);


create index fk_goods_pdt on sdb_products
(
   goods_id
);


create index fk_act_pmt on sdb_promotion
(
   pmta_id
);


create index fk_order_refound on sdb_refunds
(
   order_id
);


create index fk_mem_refound on sdb_refunds
(
   member_id
);


create index fk_opt_refound on sdb_refunds
(
   send_op_id
);


create index fk_order_ret_pdt on sdb_return_product
(
   order_id
);


create index fk_ret_pdt on sdb_return_product
(
   member_id
);


create index fk_idx_goods_sell_logs on sdb_sell_logs
(
   goods_id
);


create index fk_sendbox on sdb_sendbox
(
   tmpl_name
);


create index fk_spec_value on sdb_spec_values
(
   spec_id
);


create index fk_tag_rel on sdb_tag_rel
(
   tag_id
);


create index fk_goods_type on sdb_type_brand
(
   type_id
);


create index fk_brand_type on sdb_type_brand
(
   brand_id
);


create index fk_ws_goods on sdb_ws_goods
(
   ws_id
);


create index fk_ws_goods_cat on sdb_ws_goods_cat
(
   ws_id
);

