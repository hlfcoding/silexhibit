function delbg(filename)
{
	$.post('?a='+action, { id : ide, del_bg_img : 'true', name : filename }, function(html) 
	{
		$('div#back-img').html(html);
	});
}

var dragsort = ToolMan.dragsort();
var junkdrawer = ToolMan.junkdrawer();


function getOrder()
{
	var ord = toolOrder();
	
	$.post('?a='+action, { order : ord, upd_img_ord : 'true' }, 
		function(html) {
			updatingImgs(html);
	});
}

function saveOrder(item)
{
	// empty
}

function toolOrder()
{
	var order = junkdrawer.inspectListOrder('boxes');
	return order;
}

function getExhibit()
{
	$('#img-container').load('?a='+action+'&q=jximg&id='+ide,
		function() { 
			// need to reset things
			junkdrawer.restoreListOrder('boxes');
			dragsort.makeListSortable(document.getElementById('boxes'), saveOrder); 
			$('ul#boxes li img').mouseup( function() { getOrder(); } );
	});
}

function updateImage(ida)
{
	var title = encodeURIComponent( $('input#media_title').val() );
	var caption = encodeURIComponent( $('input#media_caption').val() );
	
	$.post('?a='+action, { upd_jximg : 'true', v : title, x : caption, id : ida }, 
		function(html) {
			//updating(html);
			getExhibit();
	});
}

function previewText(ida)
{
	var text = encodeURIComponent( $('textarea#jxcontent').val() );
	
	$.post('?a='+action, { upd_jxtext : 'true', v : text, id : ida }, 
		function(html) {
			window.location = '?a='+action+'&q=prv&id='+ide;
	});
}


function updateText(ida)
{
	if (typeof tinymce == 'undefined')
	{
		var text = encodeURIComponent( $('textarea#jxcontent').val() );
	}
	else
	{
		// silly that it really needs 'name' instead of 'id'
		var text = tinyMCE.getInstanceById('content').getHTML();
	}
	
	$.post('?a='+action, { upd_jxtext : 'true', v : text, id : ida }, 
		function(html) {
			updating(html);
	});
}

function deleteImage(ida)
{
	var answer = confirm('Are you sure?');
	
	if (answer) {
		$.post('?a='+action, { upd_jxdelimg : 'true', id : ida }, 
			function(html) {
				//updating(html);
				getExhibit();
		});
	}
}

function getImgPreview(ida)
{
	$('#img-container').load('?a='+action+'&q=view&id='+ida);
}


$(document).ready(function()
{
	junkdrawer.restoreListOrder('boxes');
	dragsort.makeListSortable(document.getElementById('boxes'), saveOrder);
	
	$('#ajx-thumbs li').tabpost('test_callback');
	$('#ajx-images li').tabpost('test_callback');
	$('#ajx-process li').tabpost('test_callback');
	$('#ajx-hidden li').tabpost('test_callback');
	$('#ajx-tiling li').tabpost('test_callback');
	$('#ajx-status li').tabpost('test_callback');
	$('#ajx-present').change( function() { updatePresent(); } );
	$('#ajx-year').change( function() { updateYear(); } );
	$('#ajx-break').change( function() { updateBreak(); } );
	$('#plugin').mouseup( function() { getColor(); } );
	$('ul#boxes li img').mouseup( function() { getOrder(); } );


	$('.inplace1').editInPlace( { params: 'upd_jxs=true&x=title&id='+ide,
		saving_text: 'Saving...',
		url: '?a='+action,
		value_required: true,
		max_length: '35'
	});
});

function getColor()
{
	var color = $('input#colorTest').val();

	$.post('?a='+action, { upd_jxs : 'true', v : color, x : 'color', id : ide }, 
		function(html) {
			updating(html);
			return false;
	});
}

function editTitle()
{
	$('.sec-title').after('');
	$('.sec-title').css('width', '300px');
	$('.sec-title').after("<input type='text' style='width:100px;' maxlength='50' /><input type='submit' />");
}

function updatePresent()
{
	var format = $('select#ajx-present').val();
	$.post('?a='+action, { upd_jxs : 'true', v : format, x : 'present', id : ide }, 
		function(html) {
			updating(html);
			return false;
	});
}

function updateYear()
{
	var format = $('select#ajx-year').val();
	$.post('?a='+action, { upd_jxs : 'true', v : format, x : 'year', id : ide }, 
		function(html) {
			updating(html);
			return false;
	});
}

function updateBreak()
{
	var format = $('select#ajx-break').val();
	$.post('?a='+action, { upd_jxs : 'true', v : format, x : 'break', id : ide }, 
		function(html) {
			updating(html);
			return false;
	});
}

jQuery.fn.tabpost = function(callback)
{
	this.click(function()
	{
		var poster = this.parentNode.id;
		var check = this.title;
		
		$.post('?a='+action, { upd_jxs : 'true', v : check, x : poster, id : ide }, 
			function(html) {
				updating(html);
		});

		$('#' + this.parentNode.id + ' li').each(function()
		{
			$(this).tabpost_compare(check, this.title);
		});
		
		return false;
	});
}

function test_callback(content)
{
	//alert(content);
}

function updating(html)
{
	$('p#ajaxhold').append(html);
	setTimeout(fader, 1000);
}

function updatingImgs(html)
{
	$('p#imgshold').append(html);
	setTimeout(fader, 1000);
}

function updateColor()
{
	// get color
	var color = ($('#colorBox').val() == '') ? 'ffffff' : $('#colorBox').val();
	
	// no hashes allowed
	color = color.replace('#', '');
	
	$.post('?a='+action, { upd_jxs : 'true', v : color, x : 'color', id : ide }, 
		function(html) {
			updating(html);
			// update bg color box
			$('span#plugID').css('background', '#' + color);

			// update bg color text description
			$('span#colorTest2').html('#' + color);
			return false;
	});
}

jQuery.fn.tabpost_compare = function(first, second)
{
	(first == second) ? $(this).addClass('active') : $(this).removeClass();
}