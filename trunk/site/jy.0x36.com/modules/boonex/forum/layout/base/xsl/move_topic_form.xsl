<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:template match="urls" />

<xsl:template match="form">

	<div class="wnd_box">
        <div style="display:none;" id="js_code">
            var ff = document.forms['move_topic'];
            f.moveTopicSubmit (ff.elements['topic'].value, ff.elements['forum'].value, ff.elements['old_forum'].value, ff.elements['goto_new_location'].value);
        </div>

        <div class="wnd_title">
            <h2>[L[Move topic form]]</h2>
        </div>			

        <div class="wnd_content">
            <form name="move_topic" onsubmit="var x=document.getElementById('js_code').innerHTML; eval(x); return false;">

                <fieldset class="form_field_row"><legend>[L[Forum to move topic to:]]</legend>
                    <select name="forum">
                        <xsl:for-each select="forums/cat">
                            <optgroup label="{@name}">
                                <xsl:for-each select="forum">
                                    <option value="{@id}">
                                        <xsl:if test="1 = @selected"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                                        <xsl:value-of select="." />
                                    </option>
                                </xsl:for-each>
                            </optgroup>
                        </xsl:for-each>
                    </select>
                </fieldset>
                <br /><br />

                <fieldset class="form_field_row"><legend>[L[After moving go to:]]</legend>
                    <select name="goto_new_location">
                        <option value="0" selected="selected">[L[Stay here]]</option>
                        <option value="1">[L[Forum where topic is moved]]</option>
                    </select>
                </fieldset>
                <br /><br />

                <input type="hidden" name="old_forum" value="{topic/forum_id}" />
                <input type="hidden" name="topic" value="{topic/id}" />
                <input type="hidden" name="action" value="move_topic_submit" />
                <div class="forum_default_padding">
                    <input type="submit" name="submit_form" value="[L[Submit]]" onclick="var x=document.getElementById('js_code').innerHTML; eval(x); return false;" />
                    <input type="reset" value="[L[Cancel]]" onclick="f.hideHTML(); return false;" class="forum_default_margin_left" />
                </div>				
            </form>
        </div>
    </div>

</xsl:template>

</xsl:stylesheet>
