###
Silexhibit Core
===============
Dependencies: `jQuery`, `Mustache`  
###

# Define the core global namespace and do any DOM-ready setup.

# Aliases.  
# Also, don't own the `$` global.
$ = jQuery.noConflict()

# Silexhibit
# ----------
# The main namespace for the Silexhibit framework, abbreviated as `st`. Should
# contain, in addition to the class and namespace hierarchy for any Silexhibit
# apps, any foundation-level helpers.
window.Silexhibit =
  # `Silexhibit.ns1` is the attribute name decorator, or for any dasherized
  # names. Conventionally, this should be aliased locally as `_attr`. This
  # should be used for any qualifying framework-level names.
  ns1: (name) -> "st-#{name}"
  # `Silexhibit.ns2` is the event name decorator, or for any dot-spaced names.
  # Conventionally, this should be aliased locally as `_evt`. This should be
  # used for any qualifying framework-level names.
  ns2: (name) -> "#{name}.st"
  # `Silexhibit.$template` is a helper for getting the Mustache template element
  # by template `id` from `Silexhibit.$templates`. Conventionally, elements
  # should be `script[data-template-id][type="text/x-mustache"]`.
  $template: (id) -> @$templates.filter "[data-template-id=#{id}]"
  # `Silexhibit.$templates` collects all the acceptable template elements in the
  # DOM. It gets populated on DOM-ready.
  $templates: null
  # `Silexhibit.template` is the main template getter by template `id`. It
  # simply passes the template element's html through Mustache to yield the
  # compiled template.
  template: (id) -> Mustache.compile @$template(id).html()
  # `Silexhibit.partials` is the main partials getter by partial `id`. Partial
  # identifiers can be underscored, to be consistent with how they are
  # referenced in the templates themselves per server-side conventions. Partials
  # are not templates but just html. Conventionally, the yielded partials array
  # is just passed into the template function like (but separate from) the
  # template context.
  partials: (ids...) ->
    partials = {}
    # Get the template while accounting for underscored identifiers.
    partials[id] = @$template(id.replace(/_/g, '-')).html() for id in ids
    partials
  appName: null
  # `Silexhibit.events` is an event bus that can be used for writing pub-sub
  # functionality, so separated, modular logic can cleanly coordinate.
  events: $ {}
  # `Silexhibit.jsonContent` is a bridge that stores any server-side data for
  # client-side use. Conventionally, the store should not be reassigned, and
  # only the app's server-side view and the client-side app should know the name
  # of the data key.
  jsonContent: {}
  # `Silexhibit.Theme` is the theme namespace for the application. It has `ns1`,
  # `ns2`, and `events` that serve the same purpose as those of Silexhibit for
  # itself. Conventionally custom themes should be publishing any APIs in this
  # sub-namespace and not `Silexhibit`.
  Theme:
    ns1: (name) -> "tm-#{name}"
    ns2: (name) -> "#{name}.tm"
    events: $ {}

# DOM-Ready
# ---------
$ ->
  # Register all valid template elements.
  Silexhibit.$templates = $ 'script[data-template-id][type="text/x-mustache"]'
