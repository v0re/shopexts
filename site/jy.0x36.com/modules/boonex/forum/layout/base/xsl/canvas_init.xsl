<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">

<xsl:template name="canvas_init">

    <script type="text/javascript">

        <xsl:if test="'client' = /root/urls/xsl_mode">
            document.write = function (s) { };
        </xsl:if>

        tinyMCE_GZ.init({
        	plugins : 'table,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,searchreplace,xhtmlxtras,media',
        	themes : 'advanced',
        	languages : 'en',
        	disk_cache : true,
        	debug : false
        });        
        
    </script>

	<script language="javascript" type="text/javascript">
        
		function orcaSetupContent (id, body, doc) {	}
        
        tinyMCE.init({
			document_base_url : "<xsl:value-of select="/root/url_dolphin" />",
            entity_encoding : "raw",
			mode : "exact",
			elements : "tinyEditor",
			theme : "advanced",
			gecko_spellcheck : true,
			content_css : "<xsl:value-of select="/root/urls/css" />blank.css",

			remove_linebreaks : true,

			setupcontent_callback : "orcaSetupContent",

			plugins : "table,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,searchreplace,xhtmlxtras,media",
			theme_advanced_buttons1_add : "fontsizeselect,separator,forecolor,backcolor",
			theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom",
			theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
			theme_advanced_buttons3_add_before : "tablecontrols,separator",
			theme_advanced_buttons3_add : "emotions,iespell,flash,separator,print",
			theme_advanced_disable : "charmap",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_path_location : "bottom",
			plugin_insertdate_dateFormat : "%Y-%m-%d",
			plugin_insertdate_timeFormat : "%H:%M:%S",
			extended_valid_elements : "a[name|href|target|title|onclick],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|obj|param|embed],object[type|allowScriptAccess|allowNetworking|height|width|data],param[name|value|],embed[src|width|height|bgcolor|type|pluginspage|flashvars|scale|AllowScriptAccess|wmode]"
			});

	</script>

	<script language="javascript" type="text/javascript">			

		var urlXsl = '<xsl:value-of select="/root/urls/xsl" />';
        var urlImg = '<xsl:value-of select="/root/urls/img" />';
        var defTitle = "<xsl:value-of select="translate(/root/title,'&quot;','&#147;')" />";
        var isLoggedIn = '<xsl:value-of select="/root/logininfo/username" />'.length ? true : false;

        var xsl_mode = '<xsl:value-of select="/root/urls/xsl_mode" />';

        var f = new Forum ('<xsl:value-of select="base"/>', <xsl:value-of select="min_point"/>);        
		document.f = f;
		var orca_login = new Login ('<xsl:value-of select="base"/>', f);
		document.orca_login = orca_login;
		<xsl:if test="1 = /root/logininfo/admin">
			var orca_admin = new Admin ('<xsl:value-of select="base"/>', f);
			document.orca_admin = orca_admin;
        </xsl:if>
        
    </script>

</xsl:template>

</xsl:stylesheet>
