$ = jQuery.noConflict()

window.Silexhibit =
  ns1: (name) -> "st-#{name}"
  ns2: (name) -> "#{name}.st"
  $template: (id) -> @$templates.filter "[data-template-id=#{id}]"
  $templates: null
  template: (id) -> Mustache.compile @$template(id).html()
  partials: (ids...) ->
    partials = {}
    partials[id] = @$template(id.replace(/_/g, '-')).html() for id in ids
    partials
  appName: null
  events: $ {}
  jsonContent: {}
  Theme:
    ns1: (name) -> "tm-#{name}"
    ns2: (name) -> "#{name}.tm"
    events: $ {}

$ ->
  Silexhibit.$templates = $ 'script[data-template-id][type="text/x-mustache"]'
