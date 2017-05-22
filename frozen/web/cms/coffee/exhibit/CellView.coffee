###
Exhibit Cell View
###

$ = jQuery
Exhibit = Silexhibit.Exhibit

class Exhibit.CellView extends Backbone.View

  # Inherited
  # ---------

  events:
    'click .exhibit-edit': 'edit'
  tagName: 'li'

  # Own
  # ---

  @domName: 'exhibit-cell'

  # Inherited
  # ---------

  constructor: (options) ->
    @className = C.domName
    super options

  initialize: (options) ->
    @template = Silexhibit.template C.domName

  render: ->
    context = @model.toJSON()
    $html = $ @template context
    @$el.html $html.html()
    @

  # Own
  # ---

  edit: (sender) ->
    app.router.navigate "exhibit/#{@model.id}", { trigger: yes }


C = Exhibit.CellView
