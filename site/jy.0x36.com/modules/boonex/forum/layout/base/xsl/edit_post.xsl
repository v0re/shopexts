<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    
<xsl:include href="replace.xsl" />
<xsl:include href="attachments.xsl" />
<xsl:include href="signature.xsl" />

<xsl:template match="urls" />

<xsl:template match="edit_post">

    <xsl:if test="'allow' = access">

	<form action="{/root/urls/base}" enctype="multipart/form-data" name="edit_post_{post_id}" method="post" target="post_actions" onreset="f.editPostCancel({post_id}); return false;" onsubmit="tinyMCE.triggerSave(); var node = document.getElementById({post_id}); if (node.parentNode.style._height) node.parentNode.style.height = node.parentNode.style._height; tinyMCE.execCommand('mceRemoveControl', false, 'tinyEditor_{post_id}'); this.post_text.style.visible = false; return true;">
		
        <div class="forum_default_padding">

            <textarea id="tinyEditor_{post_id}" name="post_text" style="width:100%; height:216px;">&#160;</textarea>

            <xsl:call-template name="attachments">
                <xsl:with-param name="files" select="attachments" />
            </xsl:call-template>            

            <xsl:call-template name="signature">
                <xsl:with-param name="text" select="signature" />
            </xsl:call-template>

            <input type="hidden" name="action" value="edit_post" /> 
            <input type="hidden" name="post_id" value="{post_id}" /> 
            <input type="hidden" name="topic_id" value="{topic_id}" />

            <div class="forum_default_margin_top">
                <input type="submit" name="post_submit" value="[L[Submit]]" class="forum_default_margin_right" />
                <input type="reset" name="cancel" value="[L[Cancel]]" onclick="if (confirm('Are you sure ?')) document.forms['edit_post_{post_id}'].reset();" />
                <xsl:if test="'disabled' != timeout">
                    <span id="edit_timeout_{post_id}" class="edit_timeout">
                        <xsl:call-template name="replace_hash">
                            <xsl:with-param name="s" select="string('[L[Allowed edit post period is # seconds]]')"/>
                            <xsl:with-param name="r" select="timeout" />
                        </xsl:call-template>                                                
                    </span>
                    <script type="text/javascript">
                        f.editPostTimer('<xsl:value-of select="post_id" />');
                    </script>
                </xsl:if>
            </div>		

        </div>

    </form>

    </xsl:if>
    <xsl:if test="'allow' != access">
        <form name="edit_post_{post_id}">
            <div class="forum_error_msg">
                [L[Allowed edit post period is elapsed]]
            </div>
        </form>
    </xsl:if>

</xsl:template>

</xsl:stylesheet>


