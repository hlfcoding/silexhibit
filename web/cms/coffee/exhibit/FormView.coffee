###
Exhibit Form View
###

$ = jQuery
Exhibit = Silexhibit.Exhibit

class Exhibit.FormView extends Backbone.View

  # Inherited
  # ---------

  events:
    'click .exhibit-save': 'save'
    'commit.editable [data-editable]': 'inputDidChange'
    'commit.selectable [data-selectable]': 'inputDidChange'
  tagName: 'section'

  # Own
  # ---

  @domName: 'exhibit-form'
  @scratchModel: null
  @partials: null

  # Inherited
  # ---------

  constructor: (options) ->
    @className = C.domName
    super options

  initialize: (options) ->
    @template = Silexhibit.template C.domName
    @partials = Silexhibit.partials [
      'exhibit_form_section_main'
      'exhibit_form_section_meta'
      'form_text_editor'
    ]...
    # Bind handlers as needed.
    _.bindAll @, 'inputDidChange'
    # Observe model.
    if @model?
      @listenTo @model, 'sync', @render
      @listenTo @model, 'invalid', @render

  render: ->
    @scratchModel = @model.clone()
    # Render for the first time.
    if @$el.is ':empty' then @renderEntirely()
    # Or render updated fragments.
    else @renderSelectively()

    if @model.validationError?
      console.log @model.validationError
      # TODO: Display error.
    @

  # Own
  # ---

  renderEntirely: () ->
    # Render the plain view.
    $html = $ @template @templateContext(), @partials
    @$el.html $html.html()
    # Apply any components and update the render.
    # TODO: Foldable.
    @$fields = $.fn.editable.register @$el
    @$options = $.fn.selectable.register @$el

  renderSelectively: () ->
    # Use component (plugin) commands if available.
    # Generally, get the data from the model by first finding out the attribute
    # key based on the element.
    @$fields.editable 'update', ($field) =>
      text = @model.get @attributeKey($field)
      { text }
    @$options.selectable 'update', ($group) =>

  templateContext: () ->
    # Use the model attributes as the base context.
    context = @model.toJSON()
    # Add exhibit globals.
    globals = app.model.get 'exhibit'
    globals.accepted_image_mimes = globals.accepted_image_mimes.join ','
    _.extend context, globals
    # For model attributes which are selected from options, add the options to
    # our context. Note the option matching the current attribute is expected to
    # be marked. Also, this will override some previously merged globals.
    for key in Exhibit.Model.selectableKeys
      key = _.pluralize key
      context[key] = @model.getWithOptions key
    # Then add some view-specific settings.
    _.extend context,
      editor: {}
      background_image_url: @model.backgroundImageUrl()

  attributeKey: ($component) ->
    $input = $component.find '[name]:eq(0)'
    if $input.length then $input.attr 'name' else $component.data 'exhibit-attribute'

  inputDidChange: (sender) ->
    if sender instanceof $.Event
      e = sender
      # When a component detects change and sends us an event, first find out
      # the attribute key based on the event's element, and then update our
      # scratch model.
      $component = $ e.currentTarget
      attributeValue = v for own k, v of e.userInfo when _.contains(['text', 'option'], k) and v?
      @scratchModel.set @attributeKey($component), attributeValue

  save: (sender) ->
    # Ensure proper handling if this is an event handler.
    sender.preventDefault() if sender instanceof $.Event
    # Save the diff between the scratch model and the model onto the model.
    $xhr = @model.saveChanged @scratchModel.attributes
    return if not $xhr? or $xhr is no
    $xhr.fail ($xhr, status, error) =>
      # TODO: Display error.
    $xhr.done (data, status, $xhr) =>
      # TODO: Display success.

C = Exhibit.FormView
