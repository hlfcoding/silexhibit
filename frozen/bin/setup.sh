# Install dependencies in order: Composer, Node, Bower.
php /usr/local/bin/composer.phar install && \
npm install && \
grunt bower

# If we don't have CoffeeScript, install it.
if [ -z "`which coffee`" ]; then
  sudo npm install -g coffee-script && sudo ln -s /usr/local/bin/node /usr/bin/node
fi

# If our Node isn't correctly linked, link it.
if [ "`which node`" == /usr/local/bin/node ]; then
  sudo ln -s /usr/local/bin/node /usr/bin/node
fi
