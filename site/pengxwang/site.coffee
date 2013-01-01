# Namespace.
$ = jQuery
$$ = window.Site ?= {}
ns = jQuery.hlf

# Boilerplate.
$.noConflict()

# TODO: Temporary fix for legacy NDXZ
window.do_click ?= $.noop

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
  setupTip = ($el) ->
    $menuItems = $el.filter '.mn-v [title]'
    $menuItems.snapTip { snap: { toYAxis: on } }
    $el.not($menuItems).snapTip { snap: { toXAxis: on } }
  # - Initial setup.
  setupTip $ '[title]'
  # - Reset as needed.
  $('body').on 'title_changed', (evt) -> setupTip $ evt.target
