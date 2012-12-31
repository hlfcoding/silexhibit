#!/bin/bash

# Run from site/ root.

cd js
curl https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js -o "jquery.js"
curl http://modernizr.com/downloads/modernizr.js -o "modernizr.js"
curl http://underscorejs.org/underscore.js -o "underscore.js"
curl https://raw.github.com/jrburke/requirejs/master/require.js -o "require.js"

# TODO: scss includes

cd ..
