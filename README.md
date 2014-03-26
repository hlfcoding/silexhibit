# Silexhibit

## Setup

1. Install Composer.
2. In your terminal and project root:

```bash
bash ./bin/setup.sh
```

## Usage

Where to put your files for:

- A custom theme: `web/site/<theme>/`
- A custom plugin: `user/Silexhibit/Plugins/<plugin>/`
- A custom php library, update: `composer.json`.
- A custom frontend library, update: `bower.json`.
- A custom grunt task or plugin, update: `package.json`, `Gruntfile.coffee`

To test:

```bash
grunt exec:setup-tests # in another tab.
grunt test
```

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
