# Namespace.
$ = jQuery
$$ = window.Site ?= {}
ns = jQuery.hlf

# Boilerplate.
$.noConflict()

$ ->
  # - Setup navigation.
  cls = $.extend {}, ns.foldable.defaults.cls, ns.foldables.defaults.cls
  $('#navigation')
    .find('.section-title').addClass(cls.head)
      .siblings().addClass(cls.item).end()
    .end()
    .find('ul').addClass(cls.menu).end()
    .find('li.active')
      .closest('ul').addClass(cls.active).end()
    .end()
    .foldables
      autoCollapse: on
      toFeature:
        baseNum: 1
  
  # - Setup printing.
  $('.js-print').bind 'click', (evt) ->
    window.print()
    evt.preventDefault()
  
  # - Setup tooltips.
###
    var opt = {
        'tip_class': 'pjpTip',
        'stem_class': 'pjpTipStem'
    };
    $('[title]').pjpTip(opt);
    $('*').bind('title_changed', function (evt) {
        $(evt.target).pjpTip(opt);
    });
###
  