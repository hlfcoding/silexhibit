# Namespace.
$ = jQuery
$$ = window.Site ?= {}
ns = jQuery.hlf

# Boilerplate.
$.noConflict()
Modernizr.run_tests()

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
  # - Setup tooltips.
  # - Setup printing.
###
    var opt = {
        'tip_class': 'pjpTip',
        'stem_class': 'pjpTipStem'
    };
    $('[title]').pjpTip(opt);
    $('*').bind('title_changed', function (evt) {
        $(evt.target).pjpTip(opt);
    });
    $('.jPrint').bind('click', function (evt) {
        window.print();
        evt.preventDefault();
    });
###