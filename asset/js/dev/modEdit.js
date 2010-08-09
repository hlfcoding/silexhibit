// super simple tools

// Start the subroutines.
var text_enter_url      = "Enter the complete URL for the hyperlink";
var text_enter_url_name = "Enter the title of the webpage";
var text_enter_email    = "Enter the email address";
var error_no_url        = "You must enter a URL";
var error_no_title      = "You must enter a title";
var error_no_email      = "You must enter an email address";


function contentWrite(NewCode) 
{
    document.mform.content.value+=NewCode;
    document.mform.content.focus();
    return;
}

function Modbold() 
{
	add = "<strong></strong>";
	contentWrite(add);
}

function Moditalic() 
{
	add = "<em></em>";
	contentWrite(add);
}

function Modunder() 
{
	add = "<u></u>";
	contentWrite(add);
}

function Modstrike() 
{
	add = "<s></s>";
	contentWrite(add);
}

function ModInsImg(enterIMG, enterIMGw, enterIMGh)
{
	var ToAdd = "<img src='"+enterIMG+"' width='"+enterIMGw+"' height='"+enterIMGh+"' />";
	window.opener.document.mform.content.value+=ToAdd;
};

function ModInsFile(enterFile, enterFileDesc)
{
	var ToAdd = "<a href='"+enterFile+"'>"+enterFileDesc+"</a>";
	window.opener.document.mform.content.value+=ToAdd;
};

function ModInsMov(file, x, y)
{
	var ToAdd = "<object classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' codebase='http://www.apple.com/qtactivex/qtplugin.cab' width='"+x+"' height='"+y+"'>";
	ToAdd = ToAdd + "<param name='src' value='"+file+"' />";
	ToAdd = ToAdd + "<param name='controller' value='true' />";
	ToAdd = ToAdd + "<param name='autoplay' value='true' />";
	ToAdd = ToAdd + "<!--[if !IE]>-->";
	ToAdd = ToAdd + "<object type='video/quicktime' data='"+file+"' width='"+x+"' height='"+y+"'>";
	ToAdd = ToAdd + "<param name='autoplay' value='true' />";
	ToAdd = ToAdd + "<param name='controller' value='true' />";
	ToAdd = ToAdd + "</object>";
	ToAdd = ToAdd + "<!--<![endif]-->";
	ToAdd = ToAdd + "</object>";
	
	window.opener.document.mform.content.value+=ToAdd;
};

function ModInsJAR(file, x, y)
{
	var ToAdd = "<!--<![code to display applets]-->";
	ToAdd = ToAdd + "<!--[if !IE]> -->";
	ToAdd = ToAdd + "<object classid='java:"+file+".class' type='application/x-java-applet' archive='"+file+"' width='"+x+"' height='"+y+"' standby='Loading Processing software...' >";
	ToAdd = ToAdd + "<param name='archive' value='"+file+"' />";
	ToAdd = ToAdd + "<param name='mayscript' value='true' />";
	ToAdd = ToAdd + "<param name='scriptable' value='true' />";
	//ToAdd = ToAdd + "<param name='image' value='loading.gif' />";
	ToAdd = ToAdd + "<param name='boxmessage' value='Loading Processing software...' />";
	ToAdd = ToAdd + "<param name='boxbgcolor' value='#FFFFFF' />";
	ToAdd = ToAdd + "<param name='test_string' value='outer' />";
	ToAdd = ToAdd + "<!--<![endif]-->";
	ToAdd = ToAdd + "<object classid='clsid:8AD9C840-044E-11D1-B3E9-00805F499D93' codebase='http://java.sun.com/update/1.4.2/jinstall-1_4_2_12-windows-i586.cab' width='"+x+"' height='"+y+"' standby='Loading Processing software...' >";
	ToAdd = ToAdd + "<param name='code' value='"+file+"' />";
	ToAdd = ToAdd + "<param name='archive' value='"+file+"' />";
	ToAdd = ToAdd + "<param name='mayscript' value='true' />";
	ToAdd = ToAdd + "<param name='scriptable' value='true' />";
	//ToAdd = ToAdd + "<param name='image' value='loading.gif' />";
	ToAdd = ToAdd + "<param name='boxmessage' value='Loading Processing software...' />";
	ToAdd = ToAdd + "<param name='boxbgcolor' value='#FFFFFF' />";
	ToAdd = ToAdd + "<param name='test_string' value='inner' />";
	ToAdd = ToAdd + "<p><strong>This browser does not have a Java Plug-in.<br />";
	ToAdd = ToAdd + "<a href='http://java.sun.com/products/plugin/downloads/index.html' title='Download Java Plug-in'>Get the latest Java Plug-in here.</a>";
	ToAdd = ToAdd + "</strong></p>";
	ToAdd = ToAdd + "</object>";
	ToAdd = ToAdd + "<!--[if !IE]> -->";
	ToAdd = ToAdd + "</object>";
	ToAdd = ToAdd + "<!--<![endif]-->";
	ToAdd = ToAdd + "<!--<![code to display applets]-->";
	
	window.opener.document.mform.content.value+=ToAdd;
}

function ModInsMP3(file)
{
	var ToAdd = "<object type='audio/mpeg' data='"+file+"' width='200' height='20'>";
	ToAdd = ToAdd + "<param name='src' value='"+file+"'>";
	ToAdd = ToAdd + "<param name='autoplay' value='true'>";
	ToAdd = ToAdd + "<param name='autoStart' value='0'>";
	ToAdd = ToAdd + "alt : <a href='"+file+"'>"+file+"</a>";
	ToAdd = ToAdd + "</object>";
	
	window.opener.document.mform.content.value+=ToAdd;
};

function ModInsChar(character)
{
	var ToAdd = character; 
	window.opener.document.mform.content.value+=ToAdd;
};

function ModInsLink(enterLink,enterLinkTitle)
{
	if (document.mformpop.selectType.value == '1') 
	{
	    var ToAdd = "<a href='"+enterLink+"'>"+enterLinkTitle+"</a>"; 
	};
	
	if (document.mformpop.selectType.value == '2') 
	{
	    var ToAdd = "<a href='mailto:"+enterLink+"'>"+enterLinkTitle+"</a>";
	};
	
	window.opener.document.mform.content.value+=ToAdd;
};

function ModSysLink(sysLink)
{
	var ToAdd = sysLink; 
	window.opener.document.mform.content.value+=ToAdd;
};


function ModTable()
{
	var width = document.mformpop.width.value;
	var rows = document.mformpop.rows.value;
	var cols = document.mformpop.cols.value;
	var cells = document.mformpop.cells.value;
	var cellp = document.mformpop.cellp.value;
	var align = document.mformpop.align.options[document.mformpop.align.selectedIndex].value;
	var border = document.mformpop.border.value;
	var ToAdd = "";
	ToAdd = ToAdd + "<table width='"+width+"' cellspacing='"+cells+"' cellpadding='"+cellp+"' align='"+align+"' border='"+border+"'>\r\n";
	for(i = 0; i < rows; i++)
	{
	ToAdd = ToAdd + "<tr>\r\n";
	for(j = 0; j < cols; j++)
	{
	"></td>\r\n"; 
	ToAdd = ToAdd + "<td valign='top' align='left'>&nbsp;</td>\r\n";
	};
	ToAdd = ToAdd + "</tr>\r\n";
	};
	ToAdd = ToAdd + "</table>";
	window.opener.document.mform.content.value+=ToAdd;
	window.close();
};

function ModList()
{
	var type = document.mformpop.type.options[document.mformpop.type.selectedIndex].value;
	var rows = document.mformpop.rows.value;
	var ToAdd = "";
	ToAdd = ToAdd + "<"+type+">\r\n";
	for(i = 0; i < rows; i++)
	{
	ToAdd = ToAdd + "<li>&nbsp;</li>\r\n";
	};
	ToAdd = ToAdd + "</"+type+">";
	window.opener.document.mform.content.value+=ToAdd;
	window.close();
};


function ModInsFlash(file, x, y)
{
	var ToAdd = "<object type='application/x-shockwave-flash' data='"+file+"' width='"+x+"' height='"+y+"'>";
	ToAdd = ToAdd + "<param name='movie' value='"+file+"' />";
	ToAdd = ToAdd + "<div style='width: "+x+"px; height: "+y+"px;'>";
	ToAdd = ToAdd + "<a href=\'http://www.adobe.com/go/gntray_dl_getflashplayer\'>";
	ToAdd = ToAdd + "Get Flash player to view this content";
	ToAdd = ToAdd + "</a>";
	ToAdd = ToAdd + "</div>";
	ToAdd = ToAdd + "</object>";
	
	window.opener.document.mform.content.value+=ToAdd;
}


function tag_url()
{
	var FoundErrors = '';
	var enterURL   = prompt(text_enter_url, "http://");
	var enterTITLE = prompt(text_enter_url_name, "My Webpage");
	
	if (!enterURL) {
		FoundErrors += " " + error_no_url;
	}
	if (!enterTITLE) {
		FoundErrors += " " + error_no_title;
	}

	if (FoundErrors) {
		alert("Error!"+FoundErrors);
		return;
	}

	ToAdd = "<a href='"+enterURL+"'>"+enterTITLE+"</a>";
	contentWrite(ToAdd);
}



function tag_email()
{
	var emailAddress = prompt(text_enter_email, "");
	var emailNAME = prompt('Enter an email name', "");

	if (!emailAddress) { 
		alert(error_no_email); 
		return; 
	}

	ToAdd = "<a mailto='"+emailAddress+"'>"+emailNAME+"</a>";
	contentWrite(ToAdd);
}