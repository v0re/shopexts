/**
*                            Orca Interactive Forum Script
*                              ---------------
*     Started             : Mon Mar 23 2006
*     Copyright           : (C) 2007 BoonEx Group
*     Website             : http://www.boonex.com
* This file is part of Orca - Interactive Forum Script
* Creative Commons Attribution 3.0 License
**/


/**
 * Enable back browser button for ajax
 */


isUseFrameForHist = 0;
if (document.all && !window.opera) 
    isUseFrameForHist = 1;
if (isUseFrameForHist && /MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
    var ieversion = new Number(RegExp.$1) // capture x.x portion and store as a number
    if (ieversion>=8)
        isUseFrameForHist = 0;
}

/**
 * constructor
 */
function BxHistory ()	
{
	this._hash = ""; // current hash (after #)
	this._to = 400; // timeout to check for history change
	this._hf = null; // hidden iframe
	this._en = '';

    this._rw = {
    
     'cat': { // categories
            'regexp' : '^group/(.*)\\.htm$', 
            'eval' : 'document.f.selectForumIndex (m[1]);',
            'pre' : 'group/',
            'ext' : '.htm'
            },

     'forum': { // forums
            'regexp' : '^forum/(.*)-(\\d+)\\.htm$', 
            'eval' : 'document.f.selectForum (m[1], m[2]);',
            'pre' : 'forum/',
            'page' : '-',
            'ext' : '.htm'
            },

     'topic': { // topics
            'regexp' : '^topic/(.*)\\.htm$', 
            'eval' : 'document.f.selectTopic (m[1]);',
            'pre' : 'topic/',
            'ext' : '.htm'
            },

     'user': { // user
            'regexp' : '^user/(.*)\\.htm$', 
            'eval' : 'document.f.showProfile (m[1]);',
            'pre' : 'user/',
            'ext' : '.htm'
            },

     'edit_cats': { // edit cats
            'regexp' : '^action=goto&edit_cats=', 
            'eval' : 'if (document.orca_admin) document.orca_admin.editCategories ();'
            },

     'new_topic': { // new topic
            'regexp' : '^action=goto&new_topic=(.*)$', 
            'eval' : 'document.f.newTopic (m[1]);'
            },

     'search': { // search
            'regexp' : '^action=goto&search=', 
            'eval' : 'document.f.showSearch ();'
            },

     'search_result': { // search results
            'regexp' : '^action=goto&search_result=1&(.*?)&(.*?)&(.*?)&(.*?)&(.*?)$', 
            'eval' : 'document.f.search (m[1], m[2], m[3], m[4], m[5]);'
            },

     'my_flags': { // my flags
            'regexp' : '^action=goto&my_flags=', 
            'eval' : 'document.f.showMyFlags ();'
            },

     'my_threads': { // my threads
            'regexp' : '^action=goto&my_threads=',
            'eval' : 'document.f.showMyThreads ();'
            },

     'recent_topics': { // forums
            'regexp' : '^recent_topics\/(\\d+)$', 
            'eval' : 'document.f.selectRecentTopics (m[1]);'
            }
    };
}

/**
 * go to the specified page - override this function to handle specific actions
 * @param h		hash (#)
 */
BxHistory.prototype.go = function (h)
{

    for (var i in this._rw)
    {
        var pattern = new RegExp(this._rw[i]['regexp']); 
        var m = h.match(pattern);
        if (!m) continue;
        eval (this._rw[i]['eval']);
        break;
    }

	return;
}

/**
 * history initialization
 * @param name		hame of history object
 */
BxHistory.prototype.init = function (name)
{
	this._en = name;

	if (isUseFrameForHist) 
        this._initHiddenFrame();

	this.handleHist ();
	window.setInterval(this._en + ".handleHist()", this._to);

	return true;
}

/**
 * handle history (ontimer function)
 */
BxHistory.prototype.handleHist =  function ()
{
	if (isUseFrameForHist)
	{
		var id = this._hf.contentDocument || this._hf.contentWindow.document;
		var h = id.getElementById('hidfr').value;

		if ( h != window.location.hash)
		{						
			this._hash = h;
			var h = this._hash.substr(1);			
			if (h.length)
			{ 
				this.go (h);
			}
			else if (!h.length && window.location.hash.length)
			{				            
				var h = window.location.hash.charAt(0) == '#' ? window.location.hash.substr(1) : window.location.hash;                
    			this.pushHist (h);
	    		this.go (h);
			} 
		} 
	}
	else 
	{
		if ( window.location.hash != this._hash )
		{			
			this._hash = window.location.hash;
			var h = this._hash.substr(1);			
			if (h.length) 
                this.go (h);
            else
                window.location = window.location;
		}
	}

	return true;
}

/**
 * record history
 * @param h	hash
 */
BxHistory.prototype.makeHist = function (h)
{
	if (h.charAt(0) != '#') h = '#' + h;
	
	if (window.location.hash == h) return;

	if (isUseFrameForHist)
	{
		var id = this._hf.contentDocument || this._hf.contentWindow.document;

		var hhh = id.getElementById('hidfr').value;		

		id.getElementById('hidfr').value = h;		

		if (h != hhh)
			this.pushHist(h);

		window.location.hash = h;
	}
	else
	{
		window.location.hash = h;
		this._hash = window.location.hash;
	}


	return true;
}

/**
 * save history : IE only
 * @param h	hash
 */
BxHistory.prototype.pushHist = function (h) 
{
	if (h.charAt(0) != '#') h = '#' + h;

	var id = this._hf.contentDocument || this._hf.contentWindow.document;

	id.write ('<input id="hidfr" value="' + h + '"/>');
	id.close();

	this._hash = window.location.hash;
}

// private -------------------------------------------

/**
 * init hidden frame : IE only
 */
BxHistory.prototype._initHiddenFrame = function ()
{

	var b = document.body;
	var i = document.createElement('iframe');
	
	i.style.display = 'none';
	i.id = 'hidfr';

	b.appendChild(i);	

	this._hf = document.getElementById('hidfr');	

    var id = null;
    if (this._hf.contentDocument)
        id = this._hf.contentDocument
    else
    if (this._hf.contentWindow && this._hf.contentWindow.document)
	    id = this._hf.contentWindow.document;

    if (id)
    {
    	id.write ('<input id="hidfr" />');
	    id.close();
    }
}

BxHistory.prototype.rw = function (s)
{
    return this._rw[s];
}
