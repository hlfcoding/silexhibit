#!/bin/bash

# Run from site/ root.

cd js

curl https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js -o "jquery.js"
curl http://modernizr.com/downloads/modernizr.js -o "modernizr.js"
curl http://underscorejs.org/underscore.js -o "underscore.js"
curl https://raw.github.com/jrburke/requirejs/master/require.js -o "require.js"

cd ../coffee

curl https://raw.github.com/hlfcoding/hlf-jquery/latest-stable/src/jquery.hlf.tip.coffee -o "jquery.hlf.tip.coffee"
curl https://raw.github.com/hlfcoding/hlf-jquery/latest-stable/src/jquery.extension.hlf.core.coffee -o "jquery.extension.hlf.core.coffee"
curl https://raw.github.com/hlfcoding/hlf-jquery/latest-stable/src/jquery.extension.hlf.event.coffee -o "jquery.extension.hlf.event.coffee"

cd ../scss

curl https://raw.github.com/hlfcoding/hlf-css/misc/bootstrap.scss -o "bootstrap.scss"
curl https://raw.github.com/hlfcoding/hlf-css/mixins.scss -o "mixins.scss"
curl https://raw.github.com/hlfcoding/hlf-css/h5bp.scss -o "h5bp.scss"
curl https://raw.github.com/hlfcoding/hlf-jquery/latest-stable/release/jquery.hlf.tip.scss -o "jquery.hlf.tip.scss"
# fontawesome-bare.scss is a mod.

cd ..
