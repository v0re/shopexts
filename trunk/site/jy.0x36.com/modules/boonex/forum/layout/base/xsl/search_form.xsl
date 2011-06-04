<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:include href="default_access_denied.xsl" />
<xsl:include href="breadcrumbs.xsl" />
<xsl:include href="box.xsl" />

<xsl:template match="urls" />

<xsl:template match="search">

    <xsl:call-template name="breadcrumbs" />

    <xsl:call-template name="box">
        <xsl:with-param name="title">[L[Search The Forum]]</xsl:with-param>
        <xsl:with-param name="content">

            <form name="new_search" method="post" target="search" onsubmit="return f.search (this.search_text.value, (this.search_type[0].checked ? this.search_type[0].value : this.search_type[1].value), this.search_forum.value, this.search_author.value, (this.search_display[0].checked ? this.search_display[0].value : this.search_display[1].value));">

                <div class="search_box">

                    <input type="hidden" name="action" value="search" />


                    <div class="search_field">
                        [L[Search for:]]
                        <input class="sh" type="text" name="search_text" size="50" style="position:absolute;left:120px;" />
                        <span class="err" style="display:none" id="err_topic_subject">Please enter from 3 to 50 symbols</span>
                    </div>

                    <br /><br />

                    <div class="search_field">
                        [L[Where to Search:]]
                        <span class="search_input">
                            <input type="radio" name="search_type" value="tlts" style="position:static;" checked="checked"/> <label>[L[Topic Titles]]</label>
                            &#160; &#160; &#160;
                            <input type="radio" name="search_type" value="msgs" style="position:static;" /> <label>[L[Messages]]</label>
                        </span>
                    </div>

                    <br /><br />

                    <div class="search_field">
                        [L[Forum:]]
                        <span class="search_input">
                            <select class="sh" name="search_forum"> 
                                <option value="0">[L[Whole Forum]]</option>
                                <xsl:apply-templates select="categs" />
                            </select>
                        </span>
                    </div>

                    <br /><br />

                    <div class="search_field">
                        [L[Author:]] <input class="sh" type="text" name="search_author" size="50" style="position:absolute;left:120px;" />
                    </div>

                    <br /><br />

                    <div class="search_field">
                        [L[Display:]]
                        <span class="search_input">
                            <input type="radio" name="search_display" value="topics" style="position:static;" checked="checked"/> <label>[L[Topics]]</label>
                            &#160; &#160; &#160;
                            <input type="radio" name="search_display" value="posts" style="position:static;" /> <label>[L[Posts]]</label>
                        </span>
                    </div>

                    <br /><br />

                    <div class="search_field forum_default_margin_bottom">

                        <input type="submit" name="search_submit" value="[L[Submit]]" onclick="var ff=document.forms['new_search'].elements; return f.search (ff.search_text.value, (ff.search_type[0].checked ? ff.search_type[0].value : ff.search_type[1].value), ff.search_forum.value, ff.search_author.value, (ff.search_display[0].checked ? ff.search_display[0].value : ff.search_display[1].value));"/>

                    </div>

                    <iframe width="1" height="1" border="0" name="search" style="border:none;" />

                </div>

            </form>

        </xsl:with-param>        
    </xsl:call-template>

</xsl:template>


<xsl:template match="categ">
	<xsl:element name="optgroup">
		<xsl:attribute name="label"><xsl:value-of select="title" /></xsl:attribute>
        <xsl:apply-templates select="forums/forum" />
	</xsl:element>
</xsl:template>

<xsl:template match="forum">
	<xsl:element name="option">
		<xsl:attribute name="value"><xsl:value-of select="@id" /></xsl:attribute>
		<xsl:value-of select="title" />
    </xsl:element>
</xsl:template>

</xsl:stylesheet>
