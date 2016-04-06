<!-- <div id="kosz"></div> -->

<script>

function XMLConfig ()
{
	this.tytul = '';
	this.cena = 0;
}

//cooki
function Get_Cookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) )
	{
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function Set_Cookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires )
	{
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var path=(path)?path:"/";
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + //expires.toGMTString()
		( ( path ) ? ";path=" + path : "" ) + 
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}

function Delete_Cookie( name, path, domain ) {
	if ( Get_Cookie( name ) ) document.cookie = name + "=" +
			( ( path ) ? ";path=" + path : "") +
			( ( domain ) ? ";domain=" + domain : "" ) +
			";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}



function Koszyk(container, orderButton)
{
	this.koszyk=new Array();
	this.container=container;
	this.orderButton=(orderButton)?orderButton:null;
	this.cfg = new XMLConfig;
	this.checkCookie('koszyk_tai');
	this.getItems();
}

Koszyk.prototype.load = function ()
{
	if(this.orderButton) this.orderButton.style.display="none";
	//this.showItems();	
}

Koszyk.prototype.checkCookie = function (name)
{
	if(Get_Cookie('koszyk_tai') == null)
	{
		Set_Cookie('koszyk_tai','');
	}
}

Koszyk.prototype.getItems = function()
{
	var kosz;
	kosz = Get_Cookie('koszyk_tai');
	if(kosz == null)
		this.koszyk=new Array();
	else
	{
		this.koszyk = kosz.split("^");
	}
}

Koszyk.prototype.itemsToOrder = function()
{
	var k, item, rmv,a, imgArr, order='';;
	var _this=this;

	if(this.koszyk != null)
	{
		this.koszyk=this.koszyk.unique();
		this.koszyk=this.koszyk.sort();
		this.koszyk.removeEmpty();
		if(this.koszyk.length > 0)
		{
			for (var key in this.koszyk)
			{
				if(!isNaN(parseInt(key)))
				{

					if(this.koszyk[key]) 
					{
						var tytul=this.koszyk[key];
						imgArr=tytul.split('|');
						if (imgArr != null)
						{
							order+='<br>'+imgArr[0];
							imgsrc=imgArr[1];
						}
					}
				}
			}
		}
	}
	return order+'<br>';
}


Koszyk.prototype.showItems = function()
{
	var k, item, rmv,a, imgArr;
	var _this=this;

	if(this.koszyk != null)
	{
		this.koszyk=this.koszyk.unique();
		this.koszyk=this.koszyk.sort();
		this.koszyk.removeEmpty();
		this.container.innerHTML='';
		if(this.koszyk.length > 0)
		{
			for (var key in this.koszyk)
			{
				if(!isNaN(parseInt(key)))
				{

					if(this.koszyk[key]) {
						var tytul=this.koszyk[key];
						var table = addTag(createTag('TABLE'), this.container);
							table.width="100%";
							table.className="koszSample";
						var tbody = addTag(createTag('TBODY'), table);
						var tr = addTag(createTag('TR'), tbody);
							
						var tdL = addTag(createTag('TD'), tr);
						var tdR = addTag(createTag('TD'), tr);
							tdR.align="right";
						
//						if(tytul) tdL.title=eval('this.cfg.'+tytul);
						imgArr=tytul.split('|');
						tdL.innerHTML=imgArr[0];
						imgsrc=imgArr[1];
						tdL.innerHTML =" <img src='"+imgsrc+"' align=absmiddle> " + tdL.innerHTML;
						a = addTag(createTag('A'), tdR);
						a.innerHTML='<? echo sysmsg('Delete','probki')?>';
						a.title='<? echo sysmsg('Delete sample from order','probki')?>';
						a._item=this.koszyk[key];
						a.href="#"
						a.onclick=function() {_this.removeItem(this);}
					}
				}
			}
			if(this.orderButton) this.orderButton.style.display="";
		}
		else
		{
			k = addTag(createTag('DIV'), this.container);
			item = addTag(createTag('SPAN'), k);
			if(this.orderButton) this.orderButton.style.display="none";
			item.innerHTML="";
		}
	}
}

Koszyk.prototype.showOrderButton = function ()
{
	this.basketEmpty=false;
}

Koszyk.prototype.putItem = function (name)
{
	if (this.koszyk.length>2)
	{
		alert("<? echo sysmsg('You can order only 3 samples','probki')?>");
		var obj=getObject("basketOrderButtonHref");
		location.href=obj.href;
		return;
	}
	if(name) this.koszyk[this.koszyk.length]=name;
	this.koszyk=this.koszyk.unique();
	Delete_Cookie('koszyk_tai');
	Set_Cookie('koszyk_tai',this.koszyk.join("^"));
	if(this.orderButton) this.orderButton.style.display="";
	//alert('Dodano do koszyka: '+name);
	//this.showItems();

	var obj=getObject("basketOrderButtonHref");
	location.href=obj.href;
}

Koszyk.prototype.removeItem = function (obj)
{
	if(obj._item)
	{
		this.koszyk.remove(obj._item);
		Delete_Cookie('koszyk_tai');
		Set_Cookie('koszyk_tai',this.koszyk.join("^"));

		this.showItems();
	}
}

Koszyk.prototype.removeAllItems = function ()
{
	this.koszyk=new Array();
	Delete_Cookie('koszyk_tai');
}


//var K = new Koszyk("kosz");

Koszyk.prototype.statusZamowienia = function ()
{
	var suma=new Number();


	if(this.koszyk.length > 0)
	{
		var p=addTag(createTag('P'), this.container);
			p.innerHTML="<B>Lista zamówionych us³ug:</B>";
		var table = addTag(createTag('TABLE'), this.container);
			table.className="statusZamowienia";
		var tbody = addTag(createTag('TBODY'), table);
	}

	for (var key in this.koszyk)
	{
		if(!isNaN(parseInt(key)))
		{
			if(this.koszyk[key]) {
				var tytul=this.koszyk[key];
				var cena=this.koszyk[key]+'_cena';
				var tr = addTag(createTag('TR'), tbody);
				
				var tdL = addTag(createTag('TD'), tr);
					tdL.className="tdL";
				var tdM = addTag(createTag('TD'), tr);
					tdM.className="tdM";
				var tdR = addTag(createTag('TD'), tr);
					tdR.className="tdR";

				tdL.innerHTML=this.koszyk[key];
				if(tytul) tdM.innerHTML=eval('this.cfg.'+tytul);
				tdR.innerHTML=" ";

			}
		}
	}

	if(this.koszyk.length > 0)
	{
		var tr = addTag(createTag('TR'), tbody);
				
		var tdL = addTag(createTag('TD'), tr);
			tdL.className="tdLs";
		var tdM = addTag(createTag('TD'), tr);
			tdM.className="tdMs";
		var tdR = addTag(createTag('TD'), tr);
			tdR.className="tdRs";

		tdL.innerHTML=" ";
		tdM.innerHTML="suma:";
		tdR.innerHTML=suma;
		
	}
}

Array.prototype.push = function() 
{
	for( var i = 0, b = this.length, a = arguments, l = a.length; i<l; i++ ) 
	{
		this[b+i] = a[i];
	}
	return this.length;
};

Array.prototype.indexOf = function( v, b, s ) 
{
	for( var i = +b || 0, l = this.length; i < l; i++ ) 
	{
		if( this[i]===v || s && this[i]==v ) return i; 
	}
	return -1;
};

Array.prototype.unique = function( b ) 
{
	var a = [], i, l = this.length;
	for( i=0; i<l; i++ ) 
	{
		if( a.indexOf( this[i], 0, b ) < 0 )  a.push( this[i] ); 
	}
	return a;
};

Array.prototype.clear = function () {
    this.length = 0;
};

Array.prototype.push2 = function (element) {
    this[this.length] = element;
    return this.length;
};


Array.prototype.remove = function (element) {
	var result = false;
	var array = [];
	for (var i = 0; i < this.length; i++) {
		if (this[i] == element) {
			result = true;
		} else {
			array.push2(this[i]);
		}
	}
	this.clear();
	for (var i = 0; i < array.length; i++) {
		this.push(array[i]);
	}
	array = null;
	return result;
};

Array.prototype.removeEmpty = function () {
	var array = [];
	for (var i = 0; i < this.length; i++) {
		if (this[i] != '') {
			array.push2(this[i]);
		}
	}
	this.clear();
	for (var i = 0; i < array.length; i++) {
		this.push(array[i]);
	}
};

//*******************************

function getObject (objectId) {
	// cross-browser function to get an object's style object given its id
	if(document.getElementById && document.getElementById(objectId)) {
	// W3C DOM
	return document.getElementById(objectId);
	} else if (document.all && document.all(objectId)) {
	// MSIE 4 DOM
	return document.all(objectId);
	} else if (document.layers && document.layers[objectId]) {
	// NN 4 DOM.. note: this won't find nested layers
	return document.layers[objectId];
	} else {
	return false;
	}
} // getObject

function createTag (tagName)
{
	var tag_handler = document.createElement(tagName);
	return tag_handler;
}

function addTag (tagHandler, where)
{
	where.appendChild(tagHandler);
	return tagHandler;
}


</script>

