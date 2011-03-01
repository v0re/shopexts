<?php

/*
	[kuaso!] (C)209-2010 Kuaso Inc.
	
	This is NOT a freeware, use is subject to license terms

	$Id: guide.php 2010-01-24 16:17:18Z anjel $
*/

require "../global.php";
?>
<HTML>
<HEAD>
<TITLE><?php echo $config["name"];?>搜索帮助中心-网页搜索帮助-站长FAQ</TITLE>
<META content="text/html; charset=GB2312" http-equiv=content-type>
<style>
.p1 {
	FONT-SIZE: 14px; LINE-HEIGHT: 24px; FONT-FAMILY: "宋体"
}
TD {
	FONT-SIZE: 14px; LINE-HEIGHT: 24px; FONT-FAMILY: "宋体"
}
.p2 {
	FONT-SIZE: 14px; COLOR: #333333; LINE-HEIGHT: 24px
}
.p3 {
	FONT-SIZE: 14px; COLOR: #0033cc; LINE-HEIGHT: 24px
}
.p4 {
	FONT-SIZE: 14px; COLOR: #0033cc; LINE-HEIGHT: 24px
}
.padd10 {
	PADDING-LEFT: 10px
}
.f12 {
	FONT-SIZE: 13px; LINE-HEIGHT: 20px
}
</style>
<script language="javascript">
<!--
function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
-->
</script>
<SCRIPT language=javascript>
<!--
function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
-->
</SCRIPT>
<META name=GENERATOR content="MSHTML 8.00.6001.18812"></HEAD>
<BODY aLink=#800080 link=#0033cc topMargin=0 bgColor=#ffffff text=#000000 
vLink=#0033cc><A name=n></A>
<TABLE border=0 width="95%" align=center>
  <TBODY>
  <TR height=60>
    <TD height=69 vAlign=top width=139><A href="<?php echo $config["url"];?>/"><IMG 
      border=0 src="../images/logo.gif"></A></TD>
    <TD vAlign=bottom>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR bgColor=#e5ecf9>
          <TD height=24>&nbsp;<B class=p1>网页搜索帮助-站长FAQ</B></TD>
          <TD class=p2 height=24>
            <DIV align=right><A 
            href="<?php echo $config["url"];?>/">帮助中心</A> 
          &nbsp;</DIV></TD></TR>
        <TR>
          <TD class=p2 height=20 
colSpan=2></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
<TABLE border=0 cellSpacing=0 cellPadding=0 width="95%" align=center>
  <TBODY>
  <TR>
    <TD height=5 colSpan=3></TD></TR>
  <TR>
    <TD height=19 colSpan=3><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#1"><B>网页收录问题</B></A>　<A 
      class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#2"><B>网页排序问题</B></A>　<A 
      class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#3"><B>商业客户相关的问题</B></A>　<A 
      class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#4"><B>给站长的建站建议</B></A>　<A 
      class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#6"><B>互联网论坛收录开放协议</B></A>　<A 
      class=p3 
  href="<?php echo $config["url"];?>/search/guide.php#5"><B>其他</B></A></TD></TR></TBODY></TABLE><B 
class=p1><A name=1></A></B><BR>
<TABLE border=0 cellSpacing=0 cellPadding=0 width="95%" align=center>
  <TBODY>
  <TR>
    <TD>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%" align=center>
        <TBODY>
        <TR>
          <TD>
            <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
              <TBODY>
              <TR>
                <TD bgColor=#e5ecf9 width=750>&nbsp;<B 
              class=p1>网页收录问题</B></TD></TR>
              <TR>
                <TD><BR>
                  <OL>
                    <LI><SPAN class=p2><B>如何让我的网站（独立网站或者blog）被<?php echo $config["name"];?>收录？</B></SPAN> 
                    <BR>
                    <UL>
                      <LI><SPAN class=p2><?php echo $config["name"];?>会收录符合用户搜索体验的网站和网页。</SPAN> 
                      <LI><SPAN 
                      class=p2>为促使<?php echo $config["name"];?>Spider更快的发现您的站点，您也可以向我们提交一下您的网站的入口网址。提交地址是：<A 
                      href="<?php echo $config["url"];?>/search/url_submit.php" 
                      target=_blank><?php echo $config["url"];?>/search/url_submit.php</A>。您只需提交首页即可，无需提交详细的内容页面。</SPAN> 

                      <LI><SPAN 
                      class=p2><?php echo $config["name"];?>的网页收录机制，只和网页价值有关，与竞价排名等商业因素没有任何关联。</SPAN> 
                    </LI></UL><BR>
                    <LI><SPAN 
                    class=p2><B>如何查看我的网站是否被<?php echo $config["name"];?>收录？site语法看到的结果数是不是收录的真实数量？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN 
                      class=p2><?php echo $config["name"];?>是否已经收录您的网站可以通过执行site语法查看，直接在<?php echo $config["name"];?>搜索中输入site:您的域名，如<A 
                      href="<?php echo $config["url"];?>/s?wd=site%3Awww.kuaso.com" 
                      target=_blank>site:www.kuaso.com</A>，如果可以查询到结果，那您的网站就已经被<?php echo $config["name"];?>收录。</SPAN> 

                      <LI><SPAN class=p2>site语法得到的搜索结果数，只是一个估算的数值，仅供参考。</SPAN> 
                      </LI></UL><BR>
                    <UL></UL><BR>
                    <LI><SPAN class=p2><B>如何让我的网页不被<?php echo $config["name"];?>收录？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN class=p2><?php echo $config["name"];?>严格遵循搜索引擎Robots协议（详细内容，参见<A 
                      href="http://www.robotstxt.org/" 
                      target=_blank>http://www.robotstxt.org/</A>）。</SPAN> 
                      <LI><SPAN 
                      class=p2>您可以设置一个Robots文件以限制您的网站全部网页或者部分目录下网页不被<?php echo $config["name"];?>收录。具体写法，参见：<A 
                      href="<?php echo $config["url"];?>/" 
                      target=_blank>如何撰写Robots文件</A>。</SPAN> 
                      <LI><SPAN 
                      class=p2>如果您的网站在被<?php echo $config["name"];?>收录之后才设置Robots文件禁止抓取，那么新的Robots文件通常会在48小时内生效，生效以后的新网页，将不再建入索引。需要注意的是，robots.txt禁止收录以前<?php echo $config["name"];?>已收录的内容，从搜索结果中去除可能需要数月的时间。</SPAN> 

                      <LI><SPAN class=p2>如果您的拒绝被收录需求非常急迫，可以在<A 
                      href="<?php echo $config["url"];?>/g/#write" 
                      target=_blank>投诉中心</A>反馈，我们会尽快处理。</SPAN> </LI></UL><BR>
                    <LI><SPAN 
                    class=p2><B>为什么我的网站内一些不设链接的私密性网页，甚至是需要访问权限的网页，也会被<?php echo $config["name"];?>收录？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN 
                      class=p2>Baiduspider对网页的抓取，是通过网页与网页之间的链接实现的。</SPAN> 
                      <LI><SPAN 
                      class=p2>网页之间的链接类型，除了站点内部的页面链接之外，还有不同网站之间的互相链接。因此，某些网页即便通过您的网站内部链接无法访问到，但是，如果别人的网站上有指向这些页面的链接，那么这些页面还是会被搜索引擎所收录。</SPAN> 

                      <LI><SPAN 
                      class=p2><?php echo $config["name"];?>Spider的访问权限，和普通用户是一样的。因此，普通用户没有权限访问的内容，Spider也没有权限访问。之所以看上去某些访问权限限制内容被<?php echo $config["name"];?>收录，原因有两点：<BR>　　A. 
                      该内容在Spider访问时是没有权限限制的，但抓取之后，内容的权限发生了变化<BR>　　B. 
                      该内容有权限限制，但是由于网站安全漏洞问题，导致用户可以通过某些特殊路径直接访问。而一旦这样的路径被公布在互联网上，则Spider就会循着这条路径抓出受限内容</SPAN> 

                      <LI><SPAN 
                      class=p2>如果您不希望这些私密性内容被<?php echo $config["name"];?>收录，一方面可以通过Robots协议加以限制；另一方面，也可以通过<A 
                      href="<?php echo $config["url"];?>/g/#write" 
                      target=_blank>投诉中心</A>反馈给我们进行解决。</SPAN> </LI></UL><BR>
                    <LI><SPAN class=p2><B>为什么我的网站收录数量越来越少？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN 
                      class=p2>您的网站所在的服务器不稳定，Baiduspider在检查更新时抓取不到网页而被暂时去除。</SPAN> 

                      <LI><SPAN class=p2>您的网站不符合用户的搜索体验。</SPAN> </LI></UL><BR>
                    <LI><SPAN class=p2><B>我的网页为什么会从<?php echo $config["name"];?>搜索结果中消失？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN class=p2><?php echo $config["name"];?>并不允诺所有网页都可从<?php echo $config["name"];?>搜索到。</SPAN> 
                      <LI><SPAN 
                      class=p2>如果您的网页长时间无法从<?php echo $config["name"];?>搜索到，或者突然从<?php echo $config["name"];?>的搜索结果中消失，可能的原因有：<BR>A. 
                      您的网页不符合用户的搜索体验<BR>B. 
                      您的网站所在服务器不稳定，被<?php echo $config["name"];?>暂时性去除，稳定之后，问题会得到解决<BR>C. 
                      您的网页内容有不符合国家法律和法规规定的地方<BR>D. 其他技术性问题</SPAN> 
                      <LI><SPAN class=p2>以下的说法是错误的和毫无根据的：<BR>A. 
                      参与了<?php echo $config["name"];?>竞价排名但未续费，会从<?php echo $config["name"];?>搜索结果中消失<BR>B. 
                      参与了其他搜索引擎的广告项目，会从<?php echo $config["name"];?>搜索结果中消失<BR>C. 
                      与<?php echo $config["name"];?>旗下网站产生了竞争，会从<?php echo $config["name"];?>搜索结果中消失<BR>D. 
                      从<?php echo $config["name"];?>获得的流量太大，会从<?php echo $config["name"];?>搜索结果中消失</SPAN> </LI></UL><BR>
                    <LI><SPAN 
                    class=p2><B>什么样的网页会被<?php echo $config["name"];?>认为是没有价值而不被<?php echo $config["name"];?>收录或者从现有搜索结果中消失？</B></SPAN><BR>
                    <UL>
                      <LI><SPAN 
                      class=p2><?php echo $config["name"];?>只收录对用户有价值的网页。任何网页在搜索结果中的去留变化，都是机器算法计算和调整的结果。下述类型的网页，<?php echo $config["name"];?>明确不会欢迎：<BR>A.网页做了很多针对搜索引擎而非用户的处理，使得用户从搜索结果中看到的内容与页面实际内容完全不同，或者使得网页在搜索结果中获得了不恰当的排名，从而导致用户产生受欺骗感觉。<BR>如果您的网站中有较多这种页面，那么这可能会使您的整个网站的页面收录和排序受到影响。<BR>B. 
                      网页是复制自互联网上的高度重复性的内容。<BR>C. 网页中有不符合中国法律和法规的内容。</SPAN> 
                    </LI></UL><SPAN class=p2><BR></SPAN>
                    <LI><SPAN 
                    class=p2><B>如果我的网站因为作弊行为而从<?php echo $config["name"];?>搜索结果中消失，是否还有被重新收录可能？</B></SPAN> 
                    <SPAN class=p2><BR></SPAN>
                    <UL>
                      <LI><SPAN 
                      class=p2>凡是作出完全修正的网站，都有机会被<?php echo $config["name"];?>重新收录。<?php echo $config["name"];?>会定期对被处理站点进行自动评估，并对符合条件者重新收录。</SPAN> 

                      <LI><SPAN 
                      class=p2>需要说明的是，<?php echo $config["name"];?>技术和产品部门只对用户搜索体验负责。以下的说法都是错误的和毫无根据的：<BR>A. 
                      我成为<?php echo $config["name"];?>的广告客户或者联盟网站，就可以重新被收录<BR>B. 我给<?php echo $config["name"];?>若干钞票，就可以重新被收录<BR>C. 
                      我认识<?php echo $config["name"];?>的某某人，就可以重新被收录</SPAN> </LI></UL><SPAN 
                    class=p2><BR></SPAN>
                    <LI><SPAN class=p2><B>我的网站更新了，可是<?php echo $config["name"];?>收录的内容还没更新怎么办？</B></SPAN> 
                    <SPAN class=p2><BR></SPAN>
                    <UL>
                      <LI 
                      class=p2><?php echo $config["name"];?>会定期自动更新所有网页（包括去除死链接，更新域名变化，更新内容变化）。因此请耐心等一段时间，您的网站上的变化就会被<?php echo $config["name"];?>察觉并修正。 
                      </LI></UL><BR>
                    <LI><SPAN 
                    class=p2><B>为什么我的网站在<?php echo $config["name"];?>收录的数量和其他搜索引擎相比相差很多？</B></SPAN><BR>
                    <UL>
                      <LI class=p2>通常情况下，这是正常的现象，不同的搜索引擎判断网页价值的算法不同。 
                    </LI></UL></LI></OL></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE><A 
      id=2 name=2></A><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A></DIV>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR>
          <TD bgColor=#e5ecf9 width=750>&nbsp;<B 
        class=p1>网页排序问题</B></TD></TR></TBODY></TABLE>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR>
          <TD><BR>
            <OL>
              <LI class=p2><B>我的网站首页被收录了，但搜索网站名称却排不到第一个，怎么办？</B> <SPAN 
              class=p2><BR>答：排序算法非常复杂。我们的目标，即在于通过算法改进，让用户以最小的成本，搜索到所需要的信息。这个过程中还是会有各种各样不尽如人意的地方。我们会非常欢迎您把您遇到的困惑和问题，反馈给我们。我们的工程师，对每一个问题都会有细致的跟踪和分析，以期将之最终解决。请将您的问题通过<A 
              href="<?php echo $config["url"];?>/g/#write" 
              target=_blank>投诉中心</A>提交给我们，以协助我们改进。<BR>　　我们一直在改进搜索算法，以使得<?php echo $config["name"];?>的搜索结果更加符合用户的搜索需求。<BR><BR></SPAN>
              <LI class=p2><B>搜索某关键词，我的网页在<?php echo $config["name"];?>搜索结果的排序短期内变化剧烈，这正常吗？</B> <SPAN 
              class=p2><BR>答：通常情况下，这是正常的变化。一般来说，有三类原因导致排序发生变化：<BR>　　A. 
              特定关键词所涉及的您的网页发生了变化<BR>　　B. 特定关键词所涉及的其他网页发生了变化<BR>　　C. 
              <?php echo $config["name"];?>的排序算法发生了变化<BR><BR></SPAN>
              <LI class=p2><B>搜索某关键词，我的网页在<?php echo $config["name"];?>的排序位置，和在其他搜索引擎的排序位置，差异非常大，这正常吗？</B> 
              <SPAN 
              class=p2><BR>答：通常情况下，这是正常的现象。因为不同搜索引擎的算法，都是不同的。<BR><BR></SPAN>
              <LI class=p2><B>我请一些“SEO”来为我的网站或者网页做优化，会有什么后果？</B> <SPAN 
              class=p2><BR>答：合理的搜索引擎优化，参见<?php echo $config["name"];?>的“给站长的建站建议”。<BR>　　外界很多打着SEO旗号的公司或者个人，也许能为您的网站带来短期的排序收益，但是，这会使您将面临更大损失的风险。在您把网站资源交托给别人之后，很多SEO会使用作弊的手法来提高排名，甚至会利用您的资源进行他们个人的运营项目，最终导致您的利益受损。<BR>　　不要因为SEO们以下的说法，而冒险将自己的网站托付给他们随意处置：<BR>A. 
              我和<?php echo $config["name"];?>的人很熟，想怎么干就怎么干，没风险<BR>B. 我是搜索引擎专家，对<?php echo $config["name"];?>的算法一清二楚，玩玩火也不要紧<BR>C. 
              我把xxx、yyy、zzz这些关键词都搞到第一了，所以我是牛人啊<BR><BR></SPAN></LI></OL></TD></TR></TBODY></TABLE><A 
      id=3 name=3></A><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A></DIV>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR bgColor=#e5ecf9>
          <TD vAlign=top>&nbsp;<B class=p1>商业客户相关的问题</B></TD></TR>
        <TR>
          <TD vAlign=top><SPAN class=p2><BR></SPAN>
            <OL>
              <LI class=p2><B>我是<?php echo $config["name"];?>的竞价排名客户，如果我不续费，<?php echo $config["name"];?>是否会因此对我进行惩罚？</B> <SPAN 
              class=p2><BR>答：这是绝对不可能的。<BR>　　<?php echo $config["name"];?>的网页搜索策略的唯一标准，在于用户的搜索体验。竞价排名和网页搜索自然排名，是完全独立的两个技术服务系统。一个网站是否是<?php echo $config["name"];?>竞价排名客户，对于网页搜索自然排序无任何影响。<BR>　　如果您收到任何类似威胁的说辞，请您直接发邮件至<A 
              href="mailto:kuaso@sina.com" 
              target=_blank>kuaso@sina.com</A>举报。<BR><BR></SPAN>
              <LI class=p2><B>我是<?php echo $config["name"];?>的竞价排名客户，为什么停止续费后网站就从<?php echo $config["name"];?>消失了？</B><BR><SPAN 
              class=p2>答：网站能否被<?php echo $config["name"];?>收录只与您网站的质量有关，与竞价排名没有任何关系。网页搜索结果中的竞价排名不代表您的网站被<?php echo $config["name"];?>收录。如果您的网站从<?php echo $config["name"];?>消失了，请参考<A 
              href="<?php echo $config["url"];?>/search/guide.php#1">网页收录问题</A>的说明。<BR><BR></SPAN>
              <LI 
              class=p2><B>我的网站因为作弊而从<?php echo $config["name"];?>消失了，是否可以通过成为<?php echo $config["name"];?>竞价排名客户、广告客户或者联盟站点的方式重新被<?php echo $config["name"];?>收录？</B> 
              <SPAN 
              class=p2><BR>答：不可以。我们对网站的收录，唯一标准是用户搜索体验。被惩罚网站重新被<?php echo $config["name"];?>收录的说明，见网页收录问题7中的叙述。 
              <BR><BR></SPAN>
              <LI 
              class=p2><B>我的网站加入<?php echo $config["name"];?>竞价排名、<?php echo $config["name"];?>联盟，或者成为<?php echo $config["name"];?>的广告客户，是否能在网页的收录和排序上获得特别的照顾？</B> 
              <SPAN class=p2><BR>答：不可能。<BR><BR></SPAN></LI></OL><SPAN 
            class=p2></SPAN></TD></TR></TBODY></TABLE><B class=p1><A id=4 
      name=4></A></B><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A> </DIV>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR bgColor=#e5ecf9>
          <TD vAlign=top>&nbsp;<B class=p1>给站长的建站建议</B></TD></TR>
        <TR>
          <TD vAlign=top><SPAN class=p2><BR>
            <OL>
              <LI 
              class=p2>为每个网页添加合适的标题，如果是网站首页，则标题建议使用站点名称或者站点代表的公司、机构名称；其余的内容页面，标题建议做成与正文内容的提炼和概括，这可以让您的潜在用户通过搜索引擎结果中的标题快速访问到您的页面。<BR><BR>
              <LI class=p2>充分利用网站首页或者频道首页的 description 
              标签，提供此网页内容的概括说明，形式为&lt;meta name="description" 
              content="此网页内容的概括说明" /&gt;，这将帮助用户和搜索引擎加强对你的网站和网页的理解。<BR><BR>
              <LI 
              class=p2>网站应该有明晰的导航和层次结构，网站上重要的网页，应该能从网站比较浅层的位置找到，确保每个页面都可以通过至少一个文本链接到达。<BR><BR>
              <LI 
              class=p2>尽量使用文字而不是flash、Javascript等来显示重要的内容或链接，<?php echo $config["name"];?>暂时无法识别Flash、Javascript中的内容，这部分内容可能无法在<?php echo $config["name"];?>搜索得到；仅在flash、Javascript中包含链接指向的网页，<?php echo $config["name"];?>可能无法收录。<BR><BR>
              <LI class=p2>尽量少使用frame和iframe框架结构，通过iframe显示的内容可能会被<?php echo $config["name"];?>丢弃。<BR><BR>
              <LI class=p2>如果网站采用动态网页，减少参数的数量和控制参数的长度将有利于收录。<BR><BR>
              <LI 
class=p2>网站改版或者网站内重要页面链接发生变动时，应该将改版前的页面301永久重定向到改版后的页面。<BR><BR>
              <LI class=p2>网站更换域名，应该将旧域名的所有页面301永久重定向到新域名上对应的页面。 
            </LI></OL></SPAN><SPAN 
            class=p2>　　只有当搜索引擎、站长、互联网用户之间，能有一种默契的利益均衡，这个行业才会顺畅发展。竭泽而渔式的网站建设，只会使您与用户、与搜索引擎越来越远。搜索引擎与站长之间，宜和谐发展，共同拥抱美好的愿景。<BR><BR>　　以下是我们给出的一些网站质量方面的建议：</SPAN> 
            <SPAN class=p2>
            <OL>
              <LI 
              class=p2>网站的内容应该是面向用户的，搜索引擎也只是网站的一个普通访客，放置任何用户不可见、或者欺骗用户的内容，都可能被搜索引擎当做作弊行为，这些行为包括但不仅限于：在网页中加入隐藏文字或隐藏链接；在网页中加入与网页内容不相关的关键词；具有欺骗性跳转或重定向；专门针对搜索引擎制作桥页；针对搜索引擎利用程序生成的内容；具有大量重复无价值内容；充斥大量恶意广告或恶意代码等。<BR><BR>
              <LI class=p2><?php echo $config["name"];?>更喜欢独特的原创内容，如果您的站点内容只是从各处采集复制而成，很可能不会被<?php echo $config["name"];?>收录。<BR><BR>
              <LI 
              class=p2>谨慎设置您的友情链接，如果您网站上的友情链接，多是指向一些垃圾站点，那么您的站点可能会受到一些负面影响。<BR><BR>
              <LI 
              class=p2>谨慎加入频道共建、内容联盟等不能产生或很少产生原创内容的计划，除非您能为内容联盟创造原创的内容。<BR><BR>
              <LI 
              class=p2><?php echo $config["name"];?>会尽量收录提供不同信息的网页，如果您网站上相同的内容可以通过不同形式展现（如论坛的简版页面、打印页），可以使用robots.txt禁止spider抓取您不想向用户展现的形式，这也有助于节省您的带宽。 
              </LI></OL></SPAN></TD></TR></TBODY></TABLE><B class=p1><A id=6 
      name=6></A></B><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A> </DIV>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR bgColor=#e5ecf9>
          <TD vAlign=top>&nbsp;<B class=p1>互联网论坛收录开放协议</B></TD></TR>
        <TR>
          <TD vAlign=top><SPAN class=p2><BR>
            <OL>《互联网论坛收录开放协议》是<?php echo $config["name"];?>网页搜索制定的论坛内容收录标准，论坛网站可将论坛内发布的帖子制作成遵循此开放协议的XML格式的网页供搜索引擎索引，将论坛发布的帖子主动、及时地告知<?php echo $config["name"];?>搜索引擎。采用了《互联网论坛收录开放协议》，就相当于论坛中的帖子被搜索引擎订阅，通过<?php echo $config["name"];?>--全球最大的中文搜索引擎这个平台，网民将有可能在更大范围内更高频率地访问到您网站论坛中的帖子，进而为您的网站带来潜在的流量。<BR><A 
              href="<?php echo $config["url"];?>/search/pageop.htm" 
              target=_blank>访问互联网论坛收录开放协议页面</A><BR></OL></SPAN></TD></TR></TBODY></TABLE><B 
      class=p1><A id=5 name=5></A></B><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A> </DIV>
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR bgColor=#e5ecf9>
          <TD vAlign=top>&nbsp;<B class=p1>其他</B></TD></TR>
        <TR>
          <TD vAlign=top><SPAN class=p2><BR>
            <OL>
              <LI><B>我发给<?php echo $config["name"];?>的线上反馈，是否会得到及时的回复？</B> 
              <BR>答：<?php echo $config["name"];?>负责网页搜索质量的工作人员，虽然无法对反馈一一进行回复，但对于每一个线上反馈，都会认真仔细的阅读和归类，并及时的转给相应的负责部门处理。 
              您有任何意义或者建议，都可以通过<A href="<?php echo $config["url"];?>/g/#write" 
              target=_blank>投诉中心</A>反馈给我们。 
        <BR><BR><BR></LI></OL></SPAN></TD></TR></TBODY></TABLE><BR>
      <DIV align=right><A class=p3 
      href="<?php echo $config["url"];?>/search/guide.php#n">返回页首</A> 
  </DIV></TD></TR></TBODY></TABLE></TD></TR></TABLE>
<HR color=#dddddd SIZE=1 width="95%">

<TABLE border=0 width="95%" align=center>
  <TBODY>
  <TR>
    <TD class=p1 align=middle><FONT color=#666666>&copy; 2009 kuaso</FONT> <A 
      href="<?php echo $config["url"];?>/duty/index.html"><FONT 
      color=#666666>免责声明</FONT></A></TD></TR></TBODY></TABLE>
</BODY></HTML>
<div style="display:none"><script src="http://s82.cnzz.com/stat.php?id=1007980&web_id=1007980&online=1&show=line" language="JavaScript" charset="gb2312"></script></div>