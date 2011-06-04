// jQuery plugin - Dolphin RSS Aggregator
(function($){
	$.fn.dolRSSFeed = function(sForceUrl) {
		return this.each( function(){
			
			var $Cont = $(this);
			var iRSSID = $Cont.attr( 'rssid' );
			if( !iRSSID && sForceUrl == undefined )
				return false;
			
			var iMaxNum = parseInt( $Cont.attr( 'rssnum' ) || 0 );
			var iMemID  = parseInt( $Cont.attr( 'member' ) || 0 );

			var sFeedURL = (sForceUrl != undefined) ? sForceUrl : site_url + 'get_rss_feed.php?ID=' + iRSSID + '&member=' + iMemID;
			
            $.getFeed( {
				url: sFeedURL ,
				success: function(feed) {
					//if( window.console ) console.log( feed );

					if (feed != undefined && feed.items) {
						var sCode =
							'<div class="rss_feed_wrapper">';
						var iCount = 0;
						for( var iItemId = 0; iItemId < feed.items.length; iItemId ++ ) {
							var item = feed.items[iItemId];
							var sDate;
                            var a;
                            var oDate

                            if (null != (a = item.updated.match(/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)Z/)))
                                oDate = new Date( a[1], a[2]-1, a[3], a[4], a[5], a[6], 0 );
                            else
    							oDate = new Date( item.updated );
                            sDate = oDate.toLocaleString();	

							sCode +=
								'<div class="rss_item_wrapper">' +
									'<div class="rss_item_header">' +
										'<a href="' + item.link + '" target="_blank">' + item.title + '</a>' +
									'</div>' +
									'<div class="rss_item_info">' +
										'<span>' +
											( '<img src="' + aDolImages['clock'] + '" /> ' ) +
											sDate +
										'</span>' +
									'</div>' +
									'<div class="rss_item_desc">' + item.description + '</div>' +
								'</div>';
							
							iCount ++;
							if( iCount == iMaxNum )
								break;
						}
						
						sCode +=
							'</div>' +
                            
                            '<div class="rss_read_more">' +
                                '<img class="bot_icon_left" src="' + aDolImages['more'] + '" />' +
                                '<a href="' + feed.link + '" target="_blank" class="rss_read_more_link">' + feed.title + '</a>' +
                            '</div>' +
                            
                            '<div class="clear_both"></div>';
						
						$Cont.html( sCode );
					}
				}
			} );
			
		} );
	};
})(jQuery);
