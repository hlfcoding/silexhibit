#!/usr/bin/env bash

cd js
curl http://modernizr.com/downloads/modernizr.js -o "modernizr.js"
curl http://underscorejs.org/underscore.js -o "underscore.js"
curl https://raw.github.com/jrburke/requirejs/master/require.js -o "require.js"
cd ..