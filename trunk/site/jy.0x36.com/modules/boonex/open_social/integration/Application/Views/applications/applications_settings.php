<?
$this->template('/common/header_ajx.php');
?>

<div id="profileContentWide" style="width:95%;">

		<?
  if (! count($vars['applications'])) {
    echo "You have not yet added any applications to your profile";
  } else {
    foreach ($vars['applications'] as $app) {
      // This makes it more compatible with iGoogle type gadgets
      // since they didn't have directory titles it seems
      if (empty($app['directory_title']) && ! empty($app['title'])) {
        $app['directory_title'] = $app['title'];
      }
      echo "<div class=\"app\" style=\"height:auto;\"><div class=\"options\">";
      if (is_object(unserialize($app['settings']))) {
		$iPersonAddon = ((int)$vars['person']['id']>0) ? '/' . (int)$vars['person']['id'] : '';
        //echo "<a href=\"" . PartuzaConfig::get('web_prefix') . "/profile/appsettings{$iPersonAddon}/{$app['mod_id']}\">Settings</a><br />";
        echo "<a href=\"" . PartuzaConfig::get('web_prefix') . "/profile/appsettings{$iPersonAddon}/{$app['mod_id']}\" onclick=\"$('#app_option_{$app['mod_id']}').load(this.href).show('slow'); return false;\">Settings</a><br />";
      }
      echo "<a href=\"" . PartuzaConfig::get('web_prefix') . "/profile/removeapp/{$vars['person']['id']}/{$app['mod_id']}\">Remove</a>";
      echo "</div>
				<div class=\"app_thumbnail\">";
      if (! empty($app['thumbnail'])) {
        // ugly hack to make it work with iGoogle images
        if (substr($app['thumbnail'], 0, strlen('/ig/')) == '/ig/') {
          $app['thumbnail'] = 'http://www.google.com' . $app['thumbnail'];
        }
        echo "<img src=\"" . PartuzaConfig::get('gadget_server') . "/gadgets/proxy?url=" . urlencode($app['thumbnail']) . "\" />";
      }
      echo "</div><b>{$app['directory_title']}</b><br />{$app['description']}<br />";
      $app['author'] = trim($app['author']);
      if (! empty($app['author_email']) && !empty($app['author'])) {
        $app['author'] = "<a href=\"mailto: {$app['author_email']}\">{$app['author']}</a>";
      }
      if (! empty($app['author'])) {
        echo "By {$app['author']}";
      }
      echo "<br /><div class=\"oauth\">This gadget's OAuth Consumer Key: <i>{$app['oauth']['consumer_key']}</i> and secret: <i>{$app['oauth']['consumer_secret']}</i></div>";
	  echo <<<EOF
<div id="app_option_{$app['mod_id']}" style="display:none;">
	todo options
</div>
EOF;
      echo "</div>";
    }
  }
  ?>
</div>

<div style="clear: both"></div>
