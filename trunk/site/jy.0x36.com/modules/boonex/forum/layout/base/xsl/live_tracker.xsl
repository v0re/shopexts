<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:template match="live_tracker">

    <div class="disignBoxFirst">
        <div class="boxFirstHeader">[L[Forums Spy]]</div>
        <div class="boxContent">

            <div id="live_tracker" class="live_tracker">
                <xsl:apply-templates select="post" />    
            	<div id="live_fade" class="live_fade">
            		&#160;
            	</div>
            </div>

        </div>
    </div>

<script>
	correctPNG('live_fade')
	var ret = f.livePost(<xsl:value-of select="./post/@ts" />);
</script>

</xsl:template>


<xsl:template match="post">
    <div id="live_post_{@id}" class="live_post">		
        <img class="lp_img" src="{avatar}" />
		<div class="lp_txt"><xsl:value-of select="text" disable-output-escaping="yes" /></div>
        <div class="lp_u">
            <a href="{profile}" onclick="{onclick}"><xsl:value-of select="user" /></a>
            [L[said in]]
            <span class="lp_bc">
                <a onclick="return f.selectForumIndex('{cat/@uri}')"><xsl:attribute name="href"><xsl:value-of select="$rw_cat" /><xsl:value-of select="cat/@uri" /><xsl:value-of select="$rw_cat_ext" /></xsl:attribute><xsl:value-of select="cat" /></a>
                &gt;
                <a onclick="return f.selectForum('{forum/@uri}')"><xsl:attribute name="href"><xsl:value-of select="$rw_forum" /><xsl:value-of select="forum/@uri" /><xsl:value-of select="$rw_forum_page" />0<xsl:value-of select="$rw_forum_ext" /></xsl:attribute><xsl:value-of select="forum" /></a>
                &gt;
    			<a onclick="return f.selectTopic('{topic/@uri}')"><xsl:attribute name="href"><xsl:value-of select="$rw_topic" /><xsl:value-of select="topic/@uri" /><xsl:value-of select="$rw_topic_ext" /></xsl:attribute><xsl:value-of select="topic" /></a>
            </span>
        </div>
        <div class="lp_date"><xsl:copy-of select="date" /></div>
	</div>
</xsl:template>

</xsl:stylesheet>

