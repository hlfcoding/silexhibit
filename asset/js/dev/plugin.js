// DHTML Color Picker
// Programming by Ulyses
// ColorJack.com

/* more changes so it wouldn't affect jquery's $.post function = $( => $d( */

function $d(v) { return(document.getElementById(v)); }
function $S(v) { return($d(v).style); }
function agent(v) { return(Math.max(navigator.userAgent.toLowerCase().indexOf(v),0)); }
function toggle(v) { $S(v).display=($S(v).display=='none'?'block':'none'); }
function within(v,a,z) { return((v>=a && v<=z)?true:false); }
function XY(e,v) { var z=agent('msie')?[event.clientX+document.body.scrollLeft,event.clientY+document.body.scrollTop]:[e.pageX,e.pageY]; return(z[zero(v)]); }
function XYwin(v) { var z=agent('msie')?[document.body.clientHeight,document.body.clientWidth]:[window.innerHeight,window.innerWidth]; return(!isNaN(v)?z[v]:z); }
function zero(v) { v=parseInt(v); return(!isNaN(v)?v:0); }

/* PLUGIN */

var maxValue={'h':360,'s':100,'v':100}, HSV={0:360,1:100,2:100};
var hSV=165, wSV=162, hH=163, slideHSV={0:360,1:100,2:100}, zINDEX=15, stop=1;

function HSVslide(d,o,e) {

	function tXY(e) { tY=XY(e,1)-top; tX=XY(e)-left; }
	function mkHSV(a,b,c) { return(Math.min(a,Math.max(0,Math.ceil((parseInt(c)/b)*a)))); }
	function ckHSV(a,b) { if(within(a,0,b)) return(a); else if(a>b) return(b); else if(a<0) return('-'+oo); }
	function drag(e) { if(!stop) { if(d!='drag') tXY(e);
	
		if(d=='SVslide') { ds.left=ckHSV(tX-oo,wSV)+'px'; ds.top=ckHSV(tY-oo,wSV)+'px';
		
			slideHSV[1]=mkHSV(100,wSV,ds.left); slideHSV[2]=100-mkHSV(100,wSV,ds.top); HSVupdate();

		}
		else if(d=='Hslide') { var ck=ckHSV(tY-oo,hH), j, r='hsv', z={};
		
			ds.top=(ck-5)+'px'; slideHSV[0]=mkHSV(360,hH,ck);
 
			for(var i=0; i<=r.length-1; i++) { j=r.substr(i,1); z[i]=(j=='h')?maxValue[j]-mkHSV(maxValue[j],hH,ck):HSV[i]; }

			HSVupdate(z); $S('SV').backgroundColor='#'+hsv2hex([HSV[0],100,100]);

		}
		else if(d=='drag') { ds.left=XY(e)+oX-eX+'px'; ds.top=XY(e,1)+oY-eY+'px'; }

	}}

	if(stop) { stop=''; var ds=$S(d!='drag'?d:o);

		if(d=='drag') { var oX=parseInt(ds.left), oY=parseInt(ds.top), eX=XY(e), eY=XY(e,1); $S(o).zIndex=zINDEX++; }
		else { var left=($d(o).offsetLeft+10), top=($d(o).offsetTop+22), tX, tY, oo=(d=='Hslide')?2:4; if(d=='SVslide') slideHSV[0]=HSV[0]; }

		document.onmousemove=drag; document.onmouseup=function(){ stop=1; document.onmousemove=''; document.onmouseup=''; }; drag(e);

	}
}

// function mkColor(v) { $S('plugID').background='#'+v; }
// loadSV(); updateH('EA00FF');

function HSVupdate(v) { 
	
	v = hsv2hex(HSV = v ? v : slideHSV);
	
	mkColor(v);
	
	// changed by vaska for our needs
	$d('colorTest2').innerHTML = '#' + v;
	$d('colorTest').value = v;
	// end change
	
	return(v);

}

function loadSV() { 
	
	var z='';

	for(var i=hSV; i>=0; i--) z+="<div style=\"BACKGROUND: #"+hsv2hex([Math.round((360/hSV)*i),100,100])+";\"><br /><\/div>";
	
	$d('Hmodel').innerHTML=z;
}


function updateH(v) { 
	
	HSV = hex2hsv(v);

	$S('SV').backgroundColor = '#' + hsv2hex(Array(HSV[0], 100, 100)); 
	$S('SVslide').top = (parseInt(wSV-wSV*(HSV[2]/100))-4) + 'px'; 			
	$S('SVslide').left = parseInt(wSV*(HSV[1]/100)) + 'px';
	$S('Hslide').top = (parseInt(wH*((maxValue['h']-HSV[0])/maxValue['h']))-7) + 'px';
}

/* CONVERSIONS */

function toHex(v) { v=Math.round(Math.min(Math.max(0,v),255)); return("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16)); }
function hex2rgb(r) { return({0:parseInt(r.substr(0,2),16),1:parseInt(r.substr(2,2),16),2:parseInt(r.substr(4,2),16)}); }
function rgb2hex(r) { return(toHex(r[0])+toHex(r[1])+toHex(r[2])); }
function hsv2hex(h) { return(rgb2hex(hsv2rgb(h))); }	
function hex2hsv(v) { return(rgb2hsv(hex2rgb(v))); }

function rgb2hsv(r) { // easyrgb.com/math.php?MATH=M20#text20

	var max=Math.max(r[0],r[1],r[2]),delta=max-Math.min(r[0],r[1],r[2]),H,S,V;
	
	if(max!=0) { S=Math.round(delta/max*100);

		if(r[0]==max) H=(r[1]-r[2])/delta; else if(r[1]==max) H=2+(r[2]-r[0])/delta; else if(r[2]==max) H=4+(r[0]-r[1])/delta;

		var H=Math.min(Math.round(H*60),360); if(H<0) H+=360;

	}

	return({0:H?H:0,1:S?S:0,2:Math.round((max/255)*100)});

}

function hsv2rgb(r) { // easyrgb.com/math.php?MATH=M21#text21

	var R,B,G,S=r[1]/100,V=r[2]/100,H=r[0]/360;

	if(S>0) { if(H>=1) H=0;

		H=6*H; F=H-Math.floor(H);
		A=Math.round(255*V*(1.0-S));
		B=Math.round(255*V*(1.0-(S*F)));
		C=Math.round(255*V*(1.0-(S*(1.0-F))));
		V=Math.round(255*V); 

		switch(Math.floor(H)) {

			case 0: R=V; G=C; B=A; break;
			case 1: R=B; G=V; B=A; break;
			case 2: R=A; G=V; B=C; break;
			case 3: R=A; G=B; B=V; break;
			case 4: R=C; G=A; B=V; break;
			case 5: R=V; G=A; B=B; break;

		}

		return({0:R?R:0,1:G?G:0,2:B?B:0});

	}
	else return({0:(V=Math.round(V*255)),1:V,2:V});

}