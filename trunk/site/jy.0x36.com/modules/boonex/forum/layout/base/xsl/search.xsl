<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">

<xsl:include href="default_access_denied.xsl" />
<xsl:include href="default_error2.xsl" />
<xsl:include href="breadcrumbs.xsl" />
<xsl:include href="box.xsl" />

<xsl:template match="urls" />

<xsl:template match="search">

    <xsl:call-template name="breadcrumbs">
        <xsl:with-param name="link1">
            <a href="javascript:void(0);" onclick="return f.showSearch()">[L[Search]]</a> 
        </xsl:with-param>        
    </xsl:call-template>

    <xsl:variable name="menu_links">		
        <btn href="javascript:void(0);" onclick="return f.showSearch()" icon="">[L[New Search]]</btn>
    </xsl:variable>

    <xsl:call-template name="box">
        <xsl:with-param name="title">[L[Search Results For:]] '<xsl:value-of select="search_text" />'</xsl:with-param>
        <xsl:with-param name="content">

            <xsl:if test="0 = count(sr)">
                <div style="text-align:center;" class="forum_default_padding">
                    [L[There are no search results.]] <br />
                    [L[Please try search again.]]
                </div>
            </xsl:if>
            <xsl:if test="0 != count(sr)">
                <table class="forum_table_list">
                    <xsl:apply-templates select="sr" />
                </table>
            </xsl:if>

        </xsl:with-param>
        <xsl:with-param name="menu" select="exsl:node-set($menu_links)/*" />
    </xsl:call-template>

</xsl:template>


<xsl:template match="sr">
	<tr>
		<td style="width:70%;" class="forum_table_column_first forum_table_fixed_height" valign="top">

            <div class="forum_search_row">
                
                <xsl:if test="0 != string-length(p)">
                    <a class="colexp2" href="javascript: void(0);" onclick="return f.expandPost('p_{p/@id}');"><div class="colexp2"><img src="{/root/urls/img}sp.gif" /></div></a>
                </xsl:if>
                
                <span>
                    <xsl:value-of select="c" /> 
                    <xsl:if test="0 != string-length(f)">
                        &#160;<img src="{/root/urls/img}a.gif" style="width:auto; height:auto; border:none;" />&#160; 
                    </xsl:if>
                    <xsl:value-of select="f" /> 
                </span>
                <br />
                <span>
                    <a style="" href="{/root/urls/base}?action=goto&amp;topic_id={t/@uri}" onclick="return f.selectTopic('{t/@uri}');"><xsl:value-of select="t" disable-output-escaping="yes" /></a>
                </span>

            </div>

                <div id="p_{p/@id}" style="display:none" class="forum_default_margin_bottom">
                    <xsl:choose>
                        <xsl:when test="/root/urls/xsl_mode = 'server'">
                            <xsl:value-of select="p" disable-output-escaping="yes" />
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:choose>
                                <xsl:when test="system-property('xsl:vendor')='Transformiix'">
                                    <div id="{p/@id}_foo" style="display:none;"><xsl:value-of select="p" /></div>
                                    <script type="text/javascript">
                                        var id = '<xsl:value-of select="p/@id" />';
                                        <![CDATA[
                                        var s = document.getElementById(id + '_foo').innerHTML;
                                        s = s.replace(/&#160;/gm, ' ');
                                        s = s.replace(/\x26gt;/gm, '\x3e');
                                        s = s.replace(/\x26lt;/gm, '\x3c');
                                        document.getElementById('p_' + id).innerHTML = s;
                                        ]]>
                                    </script>
                                </xsl:when>
                                <xsl:when test="system-property('xsl:vendor')='Microsoft'">
                                    <xsl:value-of select="p" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="p" disable-output-escaping="yes" />
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>

		</td>
		<td style="width:15%;" class="forum_table_column_others forum_search_cell_author_date" valign="top"><xsl:value-of select="@user" /></td>
		<td style="width:15%;" class="forum_table_column_others forum_search_cell_author_date" valign="top"><xsl:value-of select="@date" /></td>
	</tr>
</xsl:template>


</xsl:stylesheet>


