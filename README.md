# Silexhibit

## Differences from Indexhibit ~ 0.7

- Deprecated exhibit settings:
  - `color`

## Refresher

- Builds a small layer on top of Silex.
- Uses a lot of symfony2 bundles, but mainly in the CMS.
- Some classes are services, some aren't.
- Tending towards a Silexhibit bundle?

### Vendor Services

- Notables
  - Monolog
  - Doctrine (DBAL)
  - YAML Config
  - Mustache
  - Assetic

### App

- `app.php` sets up the top layer, a Silex Application.

### Controller

- Not just model-view manager and bridge, but handles assets for the view.
- Can register templaters.

### View

- Handles widgets.
- Maps model to template data.
- Readies template data and makes templater request.

### Model
