<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:include href="canvas_includes.xsl" />
<xsl:include href="canvas_init.xsl" />

<xsl:template match="root">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
	<xsl:choose>
		<xsl:when test="string-length(/root/page/posts/topic/title) &gt; 0">
			<xsl:value-of select="/root/page/posts/topic/title" /> [L[Orca Forum]]
		</xsl:when>
		<xsl:when test="string-length(/root/page/topics/forum/title) &gt; 0">
			<xsl:value-of select="/root/page/topics/forum/title" /> [L[Orca Forum]]
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="title" />
		</xsl:otherwise>
	</xsl:choose>
</title>	

<xsl:element name="base">
	<xsl:attribute name="href"><xsl:value-of select="base"/></xsl:attribute>
</xsl:element>

<xsl:call-template name="canvas_includes" />

</head>
<xsl:element name="body">
	<xsl:attribute name="onload">if(!document.body) { document.body = document.getElementById('body'); }; h = new BxHistory(); document.h = h; return h.init('h'); </xsl:attribute>
    <xsl:attribute name="id">body</xsl:attribute>

    <xsl:call-template name="canvas_init" />


	<div id="content">	

					<div id="f_top_nav">
						
						<a href="{/root/base}"><img class="logo_orca" src="{/root/urls/img}logo.gif" /></a>

						<div id="f_hello">
							&#160;
							<xsl:choose>
								<xsl:when test="string-length(logininfo/username) &gt; 0">									
										[L[Logged in as]] <b><xsl:value-of select="logininfo/username" /></b> 
                                        &#160;&#160;&#160;
                                        (
										<xsl:if test="1 = /root/logininfo/admin">
											<a href="javascript:void(0);" onclick="return orca_admin.editCategories()">[L[Manage Forum]]</a>,&#160;
                                            <a href="javascript:void(0);" onclick="return orca_admin.reportedPosts()">[L[Reported Posts]]</a>,&#160;
                                            [L[Compile Langs]]:
                                            <xsl:for-each select="langs/lang">
                                                <a href="javascript:void(0);" onclick="return orca_admin.compileLangs('{.}')"><xsl:value-of select="." /></a>
                                                <xsl:if test="position() != last()">&#160;</xsl:if>
                                            </xsl:for-each>,
										</xsl:if>										
                                        <a href="javascript:void(0);" onclick="return orca_login.logout()">[L[Logout]]</a>
                                        )
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>										
                        </div>						

                        <div id="f_nav">
                           <xsl:choose>
							<xsl:when test="string-length(logininfo/username) &gt; 0">									
                                <span style="background-image:url({/root/urls/img}btn_icon_flags.gif)">
                                    <a href="javascript:void(0);" onclick="return f.showMyFlags()">[L[My Flags]]</a>
                                </span>
                                <span style="background-image:url({/root/urls/img}btn_icon_topics.gif)">
                                    <a href="javascript:void(0);" onclick="return f.showMyThreads()">[L[My Topics]]</a>
                                </span>
                            </xsl:when>
                            <xsl:otherwise>
                                <span style="background-image:url({/root/urls/img}btn_icon_join.gif)">
                                    <a href="javascript:void(0);" onclick="orca_login.showJoinForm()">[L[Join]]</a>
                                </span>
                                <span style="background-image:url({/root/urls/img}btn_icon_login.gif)">
                                    <a href="javascript:void(0);" onclick="orca_login.showLoginForm()">[L[Login]]</a>
                                </span>
                            </xsl:otherwise>
                            </xsl:choose>
                                <span style="background-image:url({/root/urls/img}btn_icon_search.gif)">
                                    <a href="javascript:void(0);" onclick="return f.showSearch()">[L[Search]]</a>
                                </span>                        
                        </div>						

					</div>

					<div id="f_head" style="background-image:url({/root/urls/img}grad_bg.png);">
						&#160;
					</div>

					<div id="orca_main">
						<xsl:if test="not(string-length(page/onload))">
						<xsl:apply-templates select="page" />
						</xsl:if>
					</div>

					<div id="bottom">
                        <xsl:if test="1 != /root/disable_boonex_footers">Powered by <a href="http://www.boonex.com/products/orca">Orca Interactive Forum Script</a>.</xsl:if>
                        [L[Copyright]]
					</div>

	</div>
	
	<script language="javascript" type="text/javascript">
		correctPNG ('f_head');
	</script>

</xsl:element>
</html>
</xsl:template>

</xsl:stylesheet>
