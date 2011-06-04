<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">

<xsl:include href="cat_forums.xsl" />
<xsl:include href="live_tracker.xsl" />
<xsl:include href="box.xsl" />

<xsl:template match="urls" />
<xsl:template match="logininfo" />

<xsl:template match="page">


    <xsl:variable name="menu_links">
<!--
		<xsl:if test="1 = /root/logininfo/admin">
            <btn href="javascript:void(0);" onclick="orca_admin.editCategories();" icon="">[L[Manage Forum]]</btn>
            <btn href="javascript:void(0);" onclick="orca_admin.reportedPosts();" icon="">[L[Reported Posts]]</btn>
            <btn href="javascript:void(0);" onclick="orca_admin.hiddenPosts();" icon="">[L[Hidden Posts]]</btn>
            <btn href="javascript:void(0);" onclick="orca_admin.hiddenTopics();" icon="">[L[Hidden Topics]]</btn>
        </xsl:if>
-->
    </xsl:variable>

    <xsl:call-template name="box">
        <xsl:with-param name="title">[L[Forums Index]]</xsl:with-param>
        <xsl:with-param name="content">

            <table class="forum_table_list forum_table_categories">
                <xsl:apply-templates select="categs" />
            </table>

        </xsl:with-param>
        <xsl:with-param name="menu" select="exsl:node-set($menu_links)/*" />
    </xsl:call-template>

    <!-- <xsl:apply-templates select="live_tracker" /> -->
	
</xsl:template>


<xsl:template match="categ">		

    <tr id="cat{@id}">
		<td>

            <a class="colexp" href="{$rw_cat}{@uri}{$rw_cat_ext}" onclick="return f.selectCat('{@uri}', 'cat{@id}');">
				<div class="colexp">
                    <xsl:if test="count(forums/forum) &gt; 0">
						<xsl:attribute name="style">background-position:0px -32px</xsl:attribute>
					</xsl:if>
					&#160;
				</div>
			</a>
            <a class="forum_cat_title" href="{$rw_cat}{@uri}{$rw_cat_ext}" onclick="return f.selectCat('{@uri}', 'cat{@id}');"><xsl:value-of select="title" disable-output-escaping="yes" /></a>

        </td>
        <td class="forum_table_column_stat">

            <xsl:call-template name="replace_hash">
                <xsl:with-param name="s" select="string('[L[# forums]]')"/>
                <xsl:with-param name="r" select="@count_forums"/>
            </xsl:call-template>

        </td>
    </tr>

    <xsl:if test="count(forums/forum) &gt; 0">
        <xsl:apply-templates select="forums/forum" />
	</xsl:if>

</xsl:template>

</xsl:stylesheet>
