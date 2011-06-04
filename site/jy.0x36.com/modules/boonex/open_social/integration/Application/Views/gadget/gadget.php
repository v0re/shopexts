<?php
if (! empty($vars['gadget']['error'])) {
  echo "<b>{$vars['gadget']['error']}</b>";
} else {
  if (! count($vars['gadget'])) {
    return;
  }
  $width = $vars['width'];
  $gadget = $vars['gadget'];
  $view = $vars['view'];
  $user_prefs = isset($gadget['user_prefs']) ? $gadget['user_prefs'] : array();

  // Fill in the default values of settings that haven't been 'set'
  $prefs = '';
  $settings = ! empty($gadget['settings']) ? unserialize($gadget['settings']) : array();
  foreach ($settings as $key => $setting) {
    if (! empty($key)) {
      $value = isset($user_prefs[$key]) ? $user_prefs[$key] : (isset($setting->default) ? $setting->default : null);
      if (isset($user_prefs[$key])) {
        unset($user_prefs[$key]);
      }
      $prefs .= '&up_' . urlencode($key) . '=' . urlencode($value);
    }
  }

  // Prepare the user preferences for inclusion in the iframe url
  foreach ($user_prefs as $name => $value) {
    // if some keys _are_ set in the db, but not in the gadget metadata, we still parse them on the url
    // (the above loop unsets the entries that matched
    if (! empty($value) && ! isset($appParams[$name])) {
      $prefs .= '&up_' . urlencode($name) . '=' . urlencode($value);
    }
  }

  $_iVisitorID = (isMember() && $_COOKIE['memberID'] > 0) ? (int)$_COOKIE['memberID'] : 0;

  // Create an encrypted security token, this is used by shindig to get the various gadget instance info like the viewer and owner
  $securityToken = BasicSecurityToken::createFromValues(
    $_iVisitorID, // owner
    $_iVisitorID,           // viewer
    $gadget['id'],                                              // app id
    PartuzaConfig::get('container'),  // domain key, shindig will check for php/config/<domain>.php for container specific configuration
    urlencode($gadget['url']),        // app url
    $gadget['mod_id']                 // mod id
  );
  $gadget_url_params = array();
  parse_str(parse_url($gadget['url'], PHP_URL_QUERY), $gadget_url_params);

  // Create the actual iframe URL, this containers a slew of query params that shindig requires to render the gadget, and for the gadget to be able to make social requests
  $iframe_url = PartuzaConfig::get('gadget_server') . '/gadgets/ifr?' . "synd=" . PartuzaConfig::get('container') . "&container=" . PartuzaConfig::get('container') . "&viewer=" . $_iVisitorID . "&owner=" . ($_iVisitorID) . "&aid=" . $gadget['id'] . "&mid=" . $gadget['mod_id'] . ((isset($_GET['nocache']) && $_GET['nocache'] == '1') || (isset($gadget_url_params['nocache']) && intval($gadget_url_params['nocache']) == 1) || isset($_GET['bpc']) && $_GET['bpc'] == '1' ? "&nocache=1" : '') . "&country=US" . "&lang=en" . "&view=" . $view . "&parent=" . urlencode("http://" . $_SERVER['HTTP_HOST']) . $prefs . (isset($_GET['appParams']) ? '&view-params=' . urlencode($_GET['appParams']) : '') . "&st=" . urlencode(base64_encode($securityToken->toSerialForm())) . "&v=" . $gadget['version'] . "&url=" . urlencode($gadget['url']) . "#rpctoken=" . rand(0, getrandmax());

  // Create some chrome, this includes a header with a title, various button for varios actions, and the actual iframe

  ?>
	<div class="gadgets-gadget-content">
		<iframe
			width="100%"
        	scrolling="<?=$gadget['scrolling'] || $gadget['scrolling'] == 'true' ? 'yes' : 'auto'?>"
        	height="<?=! empty($gadget['height']) ? $gadget['height'] : 'auto'?>"
        	style="min-height:200px;"
        	frameborder="no" src="<?=$iframe_url?>"
        	class="gadgets-gadget"
        	name="remote_iframe_<?=$gadget['ID']?>"
        	id="remote_iframe_<?=$gadget['ID']?>"
            onLoad="var oThis = this; window.setTimeout(function() { iFrameHeight(oThis, window.frames.remote_iframe_<?=$gadget['ID']?>) }, 2000);"
            ></iframe>
    </div>

<?php
}
