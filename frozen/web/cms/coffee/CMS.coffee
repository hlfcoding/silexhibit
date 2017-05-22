###
App
###

_.extend Silexhibit,
  Exhibit: {}
  Section: {}
  Setting: {}

$ = jQuery
Exhibit = Silexhibit.Exhibit

class Silexhibit.CMS extends Backbone.View

  # Inherited
  # ---------

  # Own
  # ---

  headerTemplate: null

  exhibits: null
  router: null

  $content: null
  $header: null

  # Inherited
  # ---------

  initialize: (options) ->
    @model = new Silexhibit.CMS.Model Silexhibit.jsonContent.app
    @headerTemplate = Silexhibit.template 'header'
    @exhibits = new Exhibit.Collection
    @router = new Silexhibit.Router
    @$content = @$ '.app-content'
    @$header = @$ '.app-header'
    window.app = @
    Backbone.history.start
      pushState: on
      root: '/st-studio/'

  render: ->
    @renderHeader()
    @

  # Own
  # ---

  renderHeader: ->
    @$header.html @headerTemplate @model.toJSON()

  show: (view) ->
    @$content
      .empty()
      .append view.$el

class Silexhibit.CMS.Model extends Backbone.Model

  getExhibit: (key) -> @get('exhibit')[key]
