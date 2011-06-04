<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:include href="rewrite.xsl" />
<xsl:include href="replace.xsl" />

<xsl:template match="urls" />

<xsl:template match="forums/forum">

        <tr cat="{@cat}">
			<td class="forum_table_column_first">

                <div class="forum_icon_title_desc">

                    <xsl:choose>
                        <xsl:when test="1 = @new">
                            <img class="forum_icon" src="{/root/urls/img}forum_new.gif" />
                        </xsl:when>
                        <xsl:otherwise>
                            <img class="forum_icon" src="{/root/urls/img}forum.png" />
                        </xsl:otherwise>
                    </xsl:choose>

                    <a class="forum_title" onclick="return f.selectForum('{uri}', 0);"><xsl:attribute name="href"><xsl:value-of select="$rw_forum" /><xsl:value-of select="uri" /><xsl:value-of select="$rw_forum_page" />0<xsl:value-of select="$rw_forum_ext" /></xsl:attribute><xsl:value-of select="title" disable-output-escaping="yes" /></a>
                    <span>
                        <xsl:value-of select="desc" disable-output-escaping="yes" />
                        <span class="forum_stat">
                            <xsl:if test="last != ''">
                                &#8226;
                                <xsl:call-template name="replace_hash">
                                    <xsl:with-param name="s" select="string('[L[last update: #]]')" />
                                    <xsl:with-param name="r" select="last" />
                                </xsl:call-template>
                            </xsl:if>
                        </span>
                    </span>

                </div>

			</td>
            <td class="forum_table_column_stat">
        
                <xsl:call-template name="replace_hash">
                    <xsl:with-param name="s" select="string('[L[# topics]]')"/>
                    <xsl:with-param name="r" select="topics"/>
                </xsl:call-template>

            </td>
		</tr>

</xsl:template>

</xsl:stylesheet>


