<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:include href="rewrite.xsl" />
<xsl:include href="box.xsl" />
<xsl:include href="attachments.xsl" />
<xsl:include href="signature.xsl" />
<xsl:include href="default_access_denied.xsl" />
<xsl:include href="breadcrumbs.xsl" />

<xsl:template match="urls" />

<xsl:template match="new_topic">


    <xsl:call-template name="breadcrumbs">
        <xsl:with-param name="link1">
            <a href="{$rw_cat}{cat/uri}{$rw_cat_ext}" onclick="return f.selectForumIndex('{cat/uri}')"><xsl:value-of select="cat/title" disable-output-escaping="yes" /></a>
        </xsl:with-param>
        <xsl:with-param name="link2">
            <a href="{$rw_forum}{forum/uri}{$rw_forum_page}0{$rw_forum_ext}" onclick="return f.selectForum('{forum/uri}', 0);"><xsl:value-of select="forum/title" disable-output-escaping="yes" /></a>
        </xsl:with-param>
    </xsl:call-template>

    <xsl:call-template name="box">
        <xsl:with-param name="title"><xsl:value-of select="forum/title" disable-output-escaping="yes" /></xsl:with-param>
        <xsl:with-param name="content">

            <div class="forum_default_padding">		

                <form action="{/root/urls/base}" enctype="multipart/form-data" name="new_topic" method="post" target="post_new_topic" onsubmit="return f.checkPostTopicValues(this.topic_subject, this.topic_text, this.signature, true);">

					<input type="hidden" name="action" value="post_new_topic" />
                    <input type="hidden" name="forum_id" value="{forum/id}" />

                    <div class="forum_default_margin_bottom">
                       [L[Topic subject:]] 
                        <div class="forum_field_error_message" style="display:none" id="err_topic_subject">[L[Topic subject Error]]</div>
                        <div>
                            <input class="sh" type="text" name="topic_subject" size="50" maxlength="50" /> 
                        </div>
                    </div>
					
					
					<xsl:if test="1 = @sticky">
                        <div class="forum_default_margin_bottom">
					        <span class="sticky"><input type="checkbox" name="topic_sticky" id="sticky" /><label for="sticky">[L[Sticky]]</label></span>
                        </div>
					</xsl:if>

                    [L[Topic text:]] 
                    <div class="forum_field_error_message" style="display:none" id="err_topic_text">[L[Topic text Error]]</div>
					<textarea id="tinyEditor" name="topic_text" style="width:100%; height:316px;">&#160;</textarea>

                    <xsl:call-template name="attachments">
                        <xsl:with-param name="files"></xsl:with-param>
                    </xsl:call-template>

                    <xsl:call-template name="signature">
                        <xsl:with-param name="text" select="signature" />
                    </xsl:call-template>

					<div class="forum_default_margin_top">
                        <input type="submit" name="post_submit" value="[L[Submit]]" onclick="tinyMCE.triggerSave(); if (!f.checkPostTopicValues(document.forms['new_topic'].topic_subject, document.forms['new_topic'].topic_text, document.forms['new_topic'].signature, true)) return false;" class="forum_default_margin_right" />
                        <input type="reset" name="cancel" value="[L[Cancel]]" onclick="return f.cancelNewTopic('{forum/uri}', 0)" />
					</div>

				</form>

                <iframe frameborder="0" border="0" name="post_new_topic" style="border:none; padding:0; margin:0; background-color:transparent; width:0px; height:0px;">&#160;</iframe>
    		</div>

        </xsl:with-param>
    </xsl:call-template>

</xsl:template>

</xsl:stylesheet>


