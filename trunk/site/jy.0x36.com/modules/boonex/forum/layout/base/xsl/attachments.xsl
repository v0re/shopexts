<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

    <xsl:template name="attachments">
        <xsl:param name="files" />

        <div class="forum_default_margin_top">
            [L[Attachments:]]  
            <xsl:if test="$files">
                <xsl:for-each select="$files/file">
                    <br /><input type="checkbox" name="existing_file[]" value="{@hash}" id="{@hash}" checked="checked"/><label for="{@hash}"><xsl:value-of select="." /></label>
                </xsl:for-each>
            </xsl:if>
            <div class="forum_file_attachment"><input type="file" name="attachments[]" /></div>
            <a href="javascript:void(0);" onclick="jQuery('.forum_file_attachment:last').after(jQuery('.forum_file_attachment:first').clone())">[L[Attach one more file]]</a>
        </div>

    </xsl:template>

</xsl:stylesheet>
