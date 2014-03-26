###
Exhibit Collection View
###

$ = jQuery
Exhibit = Silexhibit.Exhibit

class Exhibit.CollectionView extends Backbone.View

  # Inherited
  # ---------

  tagName: 'section'

  # Own
  # ---

  @domName: 'exhibit-collection'

  # Inherited
  # ---------

  constructor: (options) ->
    @className = C.domName
    super options

  initialize: (options) ->
    @template = Silexhibit.template C.domName
    if @collection? then @listenTo @collection, 'sync', @render

  render: ->
    @empty()
    context = { sections: @collection.sections.toJSON() }
    $html = $ @template context
    @$el.html $html.html()
    for section, i in @collection.sections.models
      $exhibits = @$ ".section-exhibits:eq(#{i})"
      exhibits = @collection.sectionExhibits section
      els = []
      for exhibit in exhibits
        cellView = @appendCell exhibit
        els.push cellView.el
      $exhibits.append els
    @

  # Own
  # ---

  appendCell: (exhibit) ->
    cellView = new Exhibit.CellView model: exhibit
    @cellViews.push cellView
    cellView.render()

  empty: ->
    @$el.empty()
    @cellViews = []


C = Exhibit.CollectionView
