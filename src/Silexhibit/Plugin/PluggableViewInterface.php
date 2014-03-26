<?php

namespace Silexhibit\Plugin;

interface PluggableViewInterface
{
  public function getPluggableHtml();
  public function setPluggableHtml($html);
  public function getPluginTemplater();
  public function addAssetsForPlugin($name);
  public function addPluginSnippet($js);
  public function getJSTemplates($view_options, $template_loader, $template_path);
}
