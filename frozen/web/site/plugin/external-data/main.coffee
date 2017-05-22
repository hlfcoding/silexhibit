###
Feed Plugin
###

$ = jQuery
_attr = Silexhibit.ns1
_evt = Silexhibit.ns2
_delegate = null
_sel = null

Plugin = Silexhibit.ExternalData =
  delegate: null
  options:
    maxItems: 12
    navigateByHover: no
    hoverNavTransactionMs: 1000
  templates:
    item: {}
  # Dependencies.
  console: debug
  date: moment

console = Plugin.console

$ ->
  Plugin.delegate = _delegate = $.extend yes,
    events: $ {}
    options: ($el) -> no
    selector: (selector, $el) -> selector
    renderItem: (attrs) -> (view = @) and ''
  , Plugin.delegate
  _sel = _delegate.selector

  $context = $ _sel '#feed-container'
  $nav = $ _sel '#feed-nav'
  $content = $context.find '.st-feed-container'

  Silexhibit.$templates.filter('[data-template-id$="-feed-item"]').each ->
    $el = $ @
    type = $el.data 'type'
    Plugin.templates.item[type] = Mustache.compile $el.html()

  opts = $.extend yes, {}, Plugin.options, _delegate.options()

  collection = new Plugin.ItemCollection [],
    maxItems: opts.maxItems

  collection.on 'sync', -> $content.removeClass 'hidden'

  listView = Plugin.listView = new Plugin.ListView
    collection: collection
    el: $context.find ".#{Plugin.ListView::className}"

  navView = Plugin.navView = new Plugin.NavView
    collection: collection
    el: $nav
    navigateByHover: opts.navigateByHover
    hoverNavTransactionMs: opts.hoverNavTransactionMs
