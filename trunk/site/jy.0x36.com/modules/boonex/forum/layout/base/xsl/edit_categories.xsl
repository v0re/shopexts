<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">

<xsl:include href="box.xsl" />
<xsl:include href="replace.xsl" />
<xsl:include href="default_access_denied.xsl" />

<xsl:template match="urls" />
<xsl:template match="logininfo" />

<xsl:template match="page">

    <xsl:variable name="menu_links">
        <!-- <btn href="{/root/urls/base}" onclick="return f.selectForumIndex()" icon="">[L[Forums]]</btn> -->
        <btn href="{/root/urls/base}" onclick="" icon="">[L[Forums]]</btn>
        <btn href="javascript:void(0);" onclick="orca_admin.newCat()" icon="{/root/urls/img}btn_icon_new_cat.gif">[L[New Group]]</btn>        
        <xsl:for-each select="../langs/lang">
            <btn href="javascript:void(0);" onclick="return orca_admin.compileLangs('{.}')" icon="">[L[Compile Lang:]]<xsl:value-of select="." /></btn>
        </xsl:for-each>
    </xsl:variable>

    <xsl:call-template name="box">
        <xsl:with-param name="title">[L[Manage Forum]]</xsl:with-param>
        <xsl:with-param name="content">

            <xsl:apply-templates />            

        </xsl:with-param>
        <xsl:with-param name="menu" select="exsl:node-set($menu_links)/*" />
    </xsl:call-template>

</xsl:template>

<xsl:template match="categs">
    <table class="forum_table_list forum_table_categories">
        <xsl:apply-templates />
    </table>
</xsl:template>

<xsl:template match="categ">

	<tr id="cat{@id}">		
        <td colspan="3">
            <div style="position:relative;">

                <a class="colexp" href="javascript:void(0);" onclick="return orca_admin.selectCat('{@uri}', 'cat{@id}');">
                    <xsl:element name="div">
                        <xsl:attribute name="class">colexp</xsl:attribute>
                        <xsl:if test="count(forum) &gt; 0">
                            <xsl:attribute name="style">background-position:0px -32px</xsl:attribute>
                        </xsl:if>
                        &#160;
                    </xsl:element>
                </a>			

                <a class="forum_cat_title" href="javascript:void(0);" onclick="return orca_admin.selectCat('{@uri}', 'cat{@id}');"><xsl:value-of select="title" disable-output-escaping="yes" /></a>

                <span class="forum_stat"> 
                    &#8226; 
                    <xsl:call-template name="replace_hash">
                        <xsl:with-param name="s" select="string('[L[# forums]]')"/>
                        <xsl:with-param name="r" select="@count_forums"/>
                    </xsl:call-template>
                    &#8226; 
                    <xsl:call-template name="replace_hash">
                        <xsl:with-param name="s" select="string('[L[# topics]]')"/>
                        <xsl:with-param name="r" select="@count_topics"/>
                    </xsl:call-template>
                    &#8226;
                    <xsl:call-template name="replace_hash">
                        <xsl:with-param name="s" select="string('[L[# posts]]')"/>
                        <xsl:with-param name="r" select="@count_posts"/>
                    </xsl:call-template>
                </span>

                <div style="position:absolute; right:8px; top:13px; width:90px;">			

                    <div title="[L[edit]]" class="icn" onmouseover="this.style.backgroundPosition='0 24px'" onmouseout="this.style.backgroundPosition='0 0'" >
                        <a href="javascript:void(0);" onclick="orca_admin.editCat ({@id})"><img src="{/root/urls/img}button_l.gif" /></a>
                        <img src="{/root/urls/img}btn_icon_edit.gif" />
                    </div>

                    <div title="[L[delete]]" class="icn" onmouseover="this.style.backgroundPosition='0 24px'" onmouseout="this.style.backgroundPosition='0 0'" >
                        <a href="javascript:void(0);" onclick="orca_admin.delCat ({@id})"><img src="{/root/urls/img}button_l.gif" /></a>
                        <img src="{/root/urls/img}btn_icon_delete.gif" />
                    </div>

                    <div title="[L[new forum]]" class="icn" onmouseover="this.style.backgroundPosition='0 24px'" onmouseout="this.style.backgroundPosition='0 0'" >
                        <a href="javascript:void(0);" onclick="orca_admin.newForum ({@id})"><img src="{/root/urls/img}button_l.gif" /></a>
                        <img src="{/root/urls/img}btn_icon_new_forum.gif" />
                    </div>

                </div>
            </div>
		</td>
	</tr>
		

</xsl:template>

</xsl:stylesheet>


