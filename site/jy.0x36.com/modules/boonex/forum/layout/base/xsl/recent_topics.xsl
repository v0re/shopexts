<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">

<xsl:include href="rewrite.xsl" />
<xsl:include href="replace.xsl" />
<xsl:include href="default_access_denied.xsl" />
<xsl:include href="box.xsl" />
<xsl:include href="paginate.xsl" />

<xsl:template match="urls" />

<xsl:template match="topics">

    <xsl:variable name="menu_links">
        <btn href="{$base_url}?action=rss_updated_topics" icon="{/root/urls/img}btn_icon_rss.gif">[L[RSS feed]]</btn>
    </xsl:variable>

    <xsl:call-template name="box">
        <xsl:with-param name="title">[L[Recent Topics]]</xsl:with-param>
        <xsl:with-param name="content">

            <xsl:choose>
                <xsl:when test="(pages and pages/@num &lt;= 0) or not(topic)">

                    <div class="forum_centered_msg">
                        [L[No topics]]
                    </div>

                </xsl:when>
                <xsl:otherwise>
                    <table class="forum_table_list">
                        <xsl:apply-templates select="topic" />
                    </table>

                    <xsl:if test="pages/p">
                        <xsl:call-template name="paginate">
                            <xsl:with-param name="pages" select="pages" />
                        </xsl:call-template>
                    </xsl:if>

                </xsl:otherwise>
            </xsl:choose>

        </xsl:with-param>
        <xsl:with-param name="menu" select="exsl:node-set($menu_links)/*" />
    </xsl:call-template>

</xsl:template>

<xsl:template match="topic">

    <tr>
        <td class="forum_table_column_first forum_table_fixed_height">

            <div class="forum_icon_title_desc">
                <xsl:choose>
                    <xsl:when test="1 = @new and 0 &lt; @sticky">
                        <img src="{/root/urls/img}topic_sticky_new.png" />
                    </xsl:when>
                    <xsl:when test="0 = @new and 0 &lt; @sticky">
                        <img src="{/root/urls/img}topic_sticky.png" />
                    </xsl:when>
                    <xsl:when test="1 = @new and 0 = @sticky and 0 = @locked">
                        <img src="{/root/urls/img}topic_new.png" />
                    </xsl:when>
                    <xsl:when test="1 = @new and 0 = @sticky and 1 = @locked">
                        <img src="{/root/urls/img}topic_lock_new.png" />
                    </xsl:when>            
                    <xsl:when test="0 = @new and 0 = @sticky and 1 = @locked">
                        <img src="{/root/urls/img}topic_lock.png" />
                    </xsl:when>                        
                    <xsl:otherwise>
                        <img src="{/root/urls/img}topic.png" />
                    </xsl:otherwise>
                </xsl:choose>

                <a class="forum_topic_title" href="{$rw_topic}{uri}{$rw_topic_ext}" onclick="return f.selectTopic('{uri}');"><xsl:value-of select="title" disable-output-escaping="yes" /></a>
                <span>
                    <span class="forum_stat">                        
                        <xsl:call-template name="replace_hash_percent">
                            <xsl:with-param name="s" select="string('[L[created by # %]]')"/>
                            <xsl:with-param name="hash" select="first_u"/>
                            <xsl:with-param name="percent" select="first_d"/>
                        </xsl:call-template>
                        &#8226;
                        <xsl:call-template name="replace_hash_percent">
                            <xsl:with-param name="s" select="string('[L[last reply by # %]]')"/>
                            <xsl:with-param name="hash" select="last_u"/>
                            <xsl:with-param name="percent" select="last_d"/>
                        </xsl:call-template>
                    </span>
                </span>
                <span>
                    <a href="{$rw_cat}{cat/@uri}{$rw_cat_ext}" onclick="return f.selectForumIndex('{cat/@uri}')"><xsl:value-of select="cat" disable-output-escaping="yes" /></a>
                    &#187;
                    <a href="{$rw_forum}{forum/@uri}{$rw_forum_page}0{$rw_forum_ext}" onclick="return f.selectForum('{forum/@uri}', 0)"><xsl:value-of select="forum" disable-output-escaping="yes" /></a>
                </span>
            </div>

        </td>
        <td class="forum_table_column_stat">
        
            <xsl:call-template name="replace_hash">
                <xsl:with-param name="s" select="string('[L[# posts]]')"/>
                <xsl:with-param name="r" select="count"/>
            </xsl:call-template>        

        </td>
    </tr>

</xsl:template>

<xsl:template match="pages/p">

    <xsl:variable name="start" select="../../pages/p[@c=1]/@start" />
    <xsl:variable name="per_page" select="../../pages/@per_page" />        
    <xsl:variable name="num" select="../../pages/@num" />        
    <xsl:variable name="start_last" select="../p[position() = last()]/@start" />
    <xsl:variable name="start_prev" select="../p[@c = 1]/@start - $per_page" />
    <xsl:variable name="start_next" select="../p[@c = 1]/@start + $per_page" />

    <xsl:call-template name="paginate_number">
        <xsl:with-param name="if_first" select="position() = 1" />
        <xsl:with-param name="if_last" select="position() = last()" />
        <xsl:with-param name="link_first" select="'?start=0'" />
        <xsl:with-param name="onclick_first">return document.f.selectRecentTopics ('0')</xsl:with-param>
        <xsl:with-param name="link_prev" select="concat($base_url, '?start=', $start_prev)" />
        <xsl:with-param name="onclick_prev">return document.f.selectRecentTopics ('<xsl:value-of select="$start_prev" />')</xsl:with-param>        
        <xsl:with-param name="link_next" select="concat($base_url, '?start=', $start_next)" />
        <xsl:with-param name="onclick_next">return document.f.selectRecentTopics ('<xsl:value-of select="$start_next" />')</xsl:with-param>
        <xsl:with-param name="link_last" select="concat($base_url, '?start=', $start_last)" />    
        <xsl:with-param name="onclick_last">return document.f.selectRecentTopics ('<xsl:value-of select="$start_last" />')</xsl:with-param>
        <xsl:with-param name="link_curr" select="concat($base_url, '?start=', @start)" />
        <xsl:with-param name="onclick_curr">return document.f.selectRecentTopics ('<xsl:value-of select="@start" />')</xsl:with-param>
        <xsl:with-param name="start" select="$start" />
        <xsl:with-param name="start_last" select="$start_last" />
        <xsl:with-param name="title" select="." />
        <xsl:with-param name="c" select="@c" />
    </xsl:call-template>

</xsl:template>

</xsl:stylesheet>


