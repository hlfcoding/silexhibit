###
Router
###

Exhibit = Silexhibit.Exhibit

class Silexhibit.Router extends Backbone.Router

  # Inherited
  # ---------

  routes:
    '(exhibits)': 'exhibits'
    'exhibit/:id': 'exhibit'

  # Own
  # ---

  # Inherited
  # ---------

  # Own
  # ---

  exhibit: (id) ->
    exhibit = app.exhibits.findWhere { id }
    unless exhibit? then return app.exhibits.fetch().done => @exhibit id # TODO: Weird.
    exhibitView = new Exhibit.FormView { model: exhibit }
    app.show exhibitView
    exhibit.set 'section', app.exhibits.exhibitSection exhibit
    # Get detail.
    exhibit.fetch
      # Set again to reset change-tracking.
      success: -> exhibit.set exhibit.attributes

  exhibits: ->
    exhibitsView = new Exhibit.CollectionView
      collection: app.exhibits
    app.show exhibitsView
    app.exhibits.fetch()

