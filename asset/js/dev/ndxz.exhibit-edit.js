/**
 * @todo use modals
 * @todo native editor behavior
 * @todo move button id to link tag
 */
// TODO - classes.TagInserter mixin
_.using('org.indexhibit.*', function () {
  /**
   * Main text editor.
   * @class
   */
  classes.TextEditor = new Class({
    Implements: [Options, Events],
    options: {
      iField: 'jxcontent',
      sButton: 'a.btn:has(img), input.btn[type="image"]',
      cButtonOver: 'btn-over',
      cButtonOut: 'btn-off'
    },
    initialize: function (s, o) {
      this.setOptions(o); var _o = this.options;
      this.$container = $(s);
      this.$toolButtons = $(_o.sButton, this.$container);
      this.setup().attach();
    },
    setup: function () {
      var _this = this, _o = this.options;
      return this;
    },
    attach: function () {
      var _this = this, _o = this.options;
      this.$toolButtons.each(function () {
        var $button = $(this),
            id = $button.find('img').attr('id'); // TODO data attr
        $button.bind({
          mouseover: function (evt) { 
            $button.removeClass(_o.cButtonOut).addClass(_o.cButtonOver); 
          },
          mouseout: function (evt) { 
            $button.removeClass(_o.cButtonOver).addClass(_o.cButtonOut); 
          },
          click: function (evt) { evt.preventDefault(); }
        });
        _this._attachToolButton($button, id);
      });
      return this;
    },
    _attachToolButton: function ($button, id) {
      var _this = this, _o = this.options;
      switch (id) { 
        case 'ed_bold': 
          $button.bind('click', function () { edInsertTag(_o.iField, 0); }); 
          break; 
        case 'ed_italic': 
          $button.bind('click', function () { edInsertTag(_o.iField, 1); }); 
          break; 
        case 'ed_underline': 
          $button.bind('click', function () { edInsertTag(_o.iField, 2); }); 
          break; 
        case 'ed_files': 
        case 'ed_links': 
        // TODO - use iframe modals instead
          $button.bind('click', function () {
            OpenWindow($button.attr('href'), 'popup', 
                $button.attr('data-popup-width'), 
                $button.attr('data-popup-width'), 'yes');
          });
          break; 
        default: break;
      }
    },
    detach: function () {
      return this;
    },
    jQuery: 'ndxzTextEditor'
  });
  /**
   * Main gallery editor.
   * @class
   */
  classes.GalleryEditor = new Class({
    Implements: [Options, Events],
    options: {
      'sPreviewGallery': '.preview-gallery',
      'sPreviewItem': '.preview-item',
      'sPreviewEdit': '.preview-edit-link'
    },
    initialize: function (s, o) {
      this.setOptions(o); var _o = this.options;
      this.$pGallery = $(_o.sPreviewGallery);
      this.$pItems = $(_o.sPreviewItem, this.$pGallery);
      this.setup().attach();
    },
    setup: function () {
      var _this = this, _o = this.options;
      return this;
    },
    attach: function () {
      var _this = this, _o = this.options;
      this.$pItems.each(function () {
        var $item = $(this);
        $(_o.sPreviewEdit, $item).bind('click', function (evt) {
          evt.preventDefault();
          _this.showEdit($image);
        });
      });
    },
    showEdit: function ($image) {
    },
    deleteImage: function () {
    },
    updateImage: function () {
    },
    jQuery: 'ndxzGalleryEditor'
  });
});
if (window.jQuery) { (function ($, nsC, nsP, nsO, nsG) { 
// ----------------------------------------
// Page Controller
// ----------------------------------------
var $page;
var editExhibitPrc = nsP.editExhibit = function () {
  var $context = $('#exhibit-form', $page),
      $elT = $('#primary-editor-group #content-editor', $context).ndxzTextEditor({}),
      $elI = $('#primary-editor-group #image-manager', $context).ndxzGalleryEditor({
        'sPreviewGallery': '#img-container',
        'sPreviewItem': '.box',
        'sPreviewEdit': '.edit-image-link'
      }),
      modT = $elT.module(),
      modI = $elI.module();
  // modI.preview();
};
var editSettingsPrc = nsP.editSettings = function () {
  var $context = $('#settings-form', $page),
      $addSection = $('#add-section-form', $context);
  $('#add-section-button').bind('click', function (evt) {
    evt.preventDefault();
    $addSection.toggleClass('hidden');
  });
};
var editSectionPrc = nsP.editSection = function () {
  var $context = $('#section-form', $page);
  $('#delete-button', $context).bind('click', function (evt) {
    evt.preventDefault();
    confirm($(this).attr('data-confirm'));
  });
};
// ----------------------------------------
// Section Controller
// ----------------------------------------
$(document).ready(function () {
  $page = $('#main');
  switch (true) {
    case $page.is(':has(#exhibit-form)'):
      editExhibitPrc();
      break;
    case $page.is(':has(#settings-form)'):
      editSettingsPrc();
      break;
    case $page.is(':has(#section-form)'):
      editSectionPrc();
      break;
  }
});

})(jQuery, org.indexhibit.classes, org.indexhibit.pages, 
  org.indexhibit.options, org.indexhibit.globals); }

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
};
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
};