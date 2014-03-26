<?php

use Behat\Mink\Exception\ExpectationException,
    Behat\MinkExtension\Context\MinkContext;

use Silexhibit\Model\ExhibitModel;
use Silexhibit\View\ExhibitView;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
  const MS_WAIT_STANDARD = 3000;

  protected $app;
  protected $model;
  protected $view;
  protected $data;
  /**
   * Initializes context.
   * Every scenario gets its own context object.
   *
   * @param array $parameters context parameters (set them up through behat.yml)
   */
  public function __construct(array $parameters)
  {
    $this->app = require __DIR__.'/../../src/app.php';
    // TODO: Testable controllers.
    $this->exhibit_model = new ExhibitModel();
    $this->exhibit_view = new ExhibitView();
    $this->data = array();
    $this->app->register($this->exhibit_model);
  }

  /**
   * @Then /^the model should return complete exhibit data for "(?P<page>[^"]*)"$/
   */
  public function theModelShouldReturnCompleteExhibitDataFor($page)
  {
    $this->data[$page] = $data = $this->exhibit_model->fetchAssoc($page);
    $ret = is_array($data) && !empty($data);
    $ret = ($ret && isset($data['object']) && isset($data['content']));
    return $ret;
  }

  /**
   * @Then /^the view should properly transform exhibit data for "(?P<page>[^"]*)"$/
   */
  public function theViewShouldProperlyTransformExhibitDataFor($page)
  {
    $data = $this->data[$page];
    $data = $this->exhibit_view->transform($data);
    foreach ($this->exhibit_view->allTransformedKeys() as $old => $new) {
      if (isset($data[$old])) {
        throw new Exception(sprintf(
          "Data isn't properly transformed, $old should be $new: %s",
          var_export($data, true)
        ));
      }
    }
  }

  /**
   * @Then /^the response should contain a full page$/
   */
  public function theResponseShouldContainAFullPage()
  {
    $selectors = array(
      "head>title:not(:empty)",
      "head>link[href*='dist/site.'][href$='.compiled.css']",
      "head>script[src$='dist/site-head.compiled.js']",
      "body>script[src*='dist/site.'][src$='.compiled.js']",
      "body>script[src$='dist/site-lib.compiled.js']",
      "body>script[src$='dist/site-lib.compiled.js']",
      "#menu",
      "#content",
    );
    $tester = $this->getMink();
    foreach ($selectors as $selector) {
      $tester->assertSession()->elementsCount('css', $selector, 1);
    }
  }

  /**
   * @Then /^the response should be in "(?P<format>[^"]*)"$/
   */
  public function theResponseShouldBeIn($format)
  {
    $output = $this->getSession()->getPage()->getContent();
    $didFail = false;
    switch ($format) {
      case 'XML':
        libxml_use_internal_errors(true);
        $data = simplexml_load_string($output);
        if (!$data) {
          $errors = libxml_get_errors();
          $didFail = !empty($errors);
          libxml_clear_errors();
        }
        break;
      case 'JSON':
        $data = json_decode($output);
        $didFail = empty($data);
        break;
      default: throw new PendingException(); break;
    }
    if ($didFail) {
      throw new Exception("Response was not $format:\n $output");
    }
  }

  /**
   * @Then /^the response header "(?P<name>[^"]*)" should be "(?P<value>[^"]*)"$/
   */
  public function theResponseHeaderShouldBe($name, $value)
  {
    $headers = $this->getSession()->getResponseHeaders();
    $name = strtolower($name);
    if (!isset($headers[$name])) {
      throw new Exception("Can't find response header: $name");
    }
    $header = $headers[$name][0];
    if ($header != $value) {
      throw new Exception("Response header $name was not $value, but: $header");
    }
  }

  /**
   * @Given /^the JSON response should be a list$/
   */
  public function theJsonResponseShouldBeAList()
  {
    $data = $this->getJSON();
    if (!is_array($data)) {
      throw new Exception(sprintf(
        'JSON response is not a list: %s',
        var_export($data, true)
      ));
    }
  }

  /**
   * @Given /^the first exhibit JSON should be complete$/
   */
  public function theFirstExhibitJsonShouldBeComplete()
  {
    $data = $this->getJSON();
    if (empty($data)) {
      return;
    }
    $expected_keys = array(
      'id',
      'title',
      'year',
      'section_name',
      'section' => array(
        'id',
        'name',
        'folder_name',
        'should_display_name',
      ),
    );
    $missing_keys = $this->getMissingKeysForArray($data[0], $expected_keys);
    if (!empty($missing_keys)) {
      throw new Exception(sprintf(
        'Exhibit JSON missing properties: %s',
        var_export($missing_keys, true)
      ));
    }
  }

  /**
   * @Given /^the exhibit detail JSON should be complete$/
   */
  public function theExhibitDetailJsonShouldBeComplete()
  {
    $data = $this->getJSON();
    $expected_keys = array(
      'id',
      'object',
      'title',
      'content',
      'tags',
      'header_html',
      'time_updated',
      'time_posted',
      'creator',
      'status',
      'process',
      'page_cache',
      'section_id',
      'url',
      'ord',
      'color',
      'background_image',
      'is_hidden',
      'is_current',
      'max_image_size',
      'thumbnail_size',
      'format',
      'should_break',
      'should_tile_background',
      'year',
      'report',
    );
    $missing_keys = $this->getMissingKeysForArray($data, $expected_keys);
    if (!empty($missing_keys)) {
      throw new Exception(sprintf(
        'Exhibit JSON missing properties: %s',
        var_export($missing_keys, true)
      ));
    }
  }

  /**
   * @Given /^the DOM is ready$/
   */
  public function theDomIsReady()
  {
    $this->getSession()->wait(self::MS_WAIT_STANDARD, "
      (jQuery.active === 0 && jQuery(':animated').length) === 0 &&
      (jQuery('.app-content > section > *').length > 0)
    ");
  }

  /**
   * @When /^I click the first "([^"]*)" element$/
   */
  public function iClickTheFirstMatchingElement($selector)
  {
    $element = $this->getSession()->getPage()->find('css', $selector);
    if (!isset($element)) {
      throw new ElementNotFoundException(
        $this->getSession(), 'element', 'selector', $selector
      );
    }
    $element->click();
  }

  /**
   * @Given /^I wait for a "([^"]*)" element$/
   */
  public function iWaitForAMatchingElement($selector)
  {
    $this->getSession()->wait(self::MS_WAIT_STANDARD, "
      jQuery('$selector').length > 0
    ");
  }

  /**
   * @Given /^I wait for the network$/
   */
  public function iWaitForTheNetwork()
  {
    $this->getSession()->wait(self::MS_WAIT_STANDARD);
  }

  /**
   * @Then /^I should not see any visible "([^"]*)" elements$/
   */
  public function iShouldNotSeeAnyVisibleMatchingElements($selector)
  {
    $element = $this->getSession()->getPage()->find('css', $selector);
    if (isset($element) && $element->isVisible()) {
      throw new ExpectationException(
        "An element matching css \"$selector\" appears on this page, but it should not."
      );
    }
  }

  protected function getJSON()
  {
    return json_decode($this->getSession()->getPage()->getContent(), true);
  }

  protected function getMissingKeysForArray($array, $keys)
  {
    $missing = array();
    foreach ($keys as $possible_group_key => $key) {
      $sub_keys = false;
      if (is_array($key)) {
        $sub_keys = $key;
        $key = $possible_group_key;
      }
      if (!isset($array[$key])) {
        $missing[] = $key;
      }
      if ($sub_keys !== false) {
        $sub_missing = $this->getMissingKeysForArray($array[$key], $sub_keys);
        if (!empty($sub_missing)) {
          $missing[$key] = $sub_missing;
        }
      }
    }
    return $missing;
  }
}
