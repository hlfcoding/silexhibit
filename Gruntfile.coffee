matchdep = require 'matchdep'

module.exports = (grunt) ->

  SOURCES =
    DOCS:
      PRIVATE: [
        'config/*.yml'
        '!config/prod.yml'
        'src/**/*.php'
        'web/site/**/*.{js,coffee,mustache}'
        'web/cms/**/*.{js,coffee,mustache}'
        'README.md'
      ]
  PATHS =
    ASSETIC_CACHE: 'tmp/cache/assetic'
    MONOLOG_LOGS: 'logs'
  URLS =
    SELENIUM: 'http://selenium.googlecode.com/files/selenium-server-standalone-2.39.0.jar'

  grunt.initConfig
    pkg: grunt.file.readJSON 'package.json'
    bower:
      install:
        options:
          cleanBowerDir: yes
          cleanTargetDir: no
          copy: yes
          install: yes
          layout: (type, component) -> type
          targetDir: 'web/lib'
          verbose: yes
    clean:
      options:
        'no-write': no
      assetic: [
        "#{PATHS.ASSETIC_CACHE}/*"
        "!#{PATHS.ASSETIC_CACHE}/.gitignore"
      ]
      monolog: [
        "#{PATHS.MONOLOG_LOGS}/*"
        "!#{PATHS.MONOLOG_LOGS}/.gitignore"
      ]
    exec:
      'setup-permissions':
        command:
          """
          chmod 777 ./logs
          chmod 777 ./web/dist
          """
      'run-tests':
        command: "
          ./bin/behat &&
          ./bin/behat ./user/Silexhibit/Plugins/**/*.feature"
      'setup-tests':
        command:
          # See: http://tinyurl.com/js-sites-w-behat
          # Stop with `ctrl + c`.
          # Also requires Homebrew to install ChromeDriver.
          """
          if [[ -z `brew list | grep chromedriver` ]]; then
            brew install chromedriver
          fi
          if [[ ! -a ./bin/selenium.jar ]]; then
            curl -o ./bin/selenium.jar #{URLS.SELENIUM}
          fi
          if [[ $? != 0 ]]; then exit $?; fi
          java -jar ./bin/selenium.jar
          """
    groc:
      private: SOURCES.DOCS.PRIVATE
      options:
        out: 'web/docs'
    watch:
      docs:
        files: SOURCES.DOCS.PRIVATE
        tasks: ['groc:private']
        options:
          spawn: no

  grunt.loadNpmTasks plugin for plugin in matchdep.filterDev 'grunt-*'

  grunt.registerTask 'default', ['bower:install']
  grunt.registerTask 'docs', ['groc', 'watch:docs']
  grunt.registerTask 'test', ['exec:run-tests']
