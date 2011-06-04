<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

    <xsl:template name="avatar">
        <xsl:param name="href" />
        <xsl:param name="thumb" />
        <xsl:param name="username" />

        <div class="thumbnail_image" style="width:36px; height:36px;">
            <a href="{$href}" title="{$username}">
            <img alt="{$username}" style="background-image:url({$thumb}); width:32px; height:32px;" src="{/root/urls/img}sp.gif"/></a>
        </div>

    </xsl:template>

</xsl:stylesheet>

