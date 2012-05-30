#!/usr/bin/env bash

# Run from site directory!

# Config:
export NDXZ_SITE_NAME=pengxwang

# Aliases:
alias ndxz-sass='cd $NDXZ_SITE_NAME; sass --watch .:css'
alias ndxz-coffee='coffee -o js/ -cw coffee/'