<?php

namespace Silexhibit\Controller;

use Silex\Application;

use Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\Generator\UrlGenerator;

use Assetic\Asset\FileAsset,
    Assetic\Asset\GlobAsset;

use Silex\Provider\SecurityServiceProvider,
    Silex\Provider\SessionServiceProvider,
    Silex\Provider\SwiftmailerServiceProvider,
    Silex\Provider\ValidatorServiceProvider;

use Silexhibit\Controller,
    Silexhibit\Model\ExhibitModel,
    Silexhibit\ModelServiceProvider,
    Silexhibit\Traits\ExhibitTransformerTrait,
    Silexhibit\View\AppView,
    Silexhibit\ViewServiceProvider;

class CMSController extends Controller
{
  use ExhibitTransformerTrait;

  protected $app_view;
  protected $exhibit_model;

  public function __construct(Application $app)
  {
    $this->app_name = 'cms';
    
    parent::__construct($app);

    $app->register(new SessionServiceProvider());
    $app->register(new ValidatorServiceProvider());
    // TODO: Requires additional setup.
    //$app->register(new SecurityServiceProvider());
    $app->register(new SwiftmailerServiceProvider());

    $this->app_view = $this->registerView(new AppView($this), $app);

    $this->exhibit_model = new ExhibitModel($this);
    $this->exhibit_model->is_public = false;
    $app->register($this->exhibit_model);
  }

  public function modelForView(ViewServiceProvider $view)
  {
    return null;
  }

  public function viewForModel(ModelServiceProvider $model)
  {
    return null;
  }

  public function provideData($key, array $params=array())
  {
    $relative_key = substr($key, (strpos($key, '_') + 1));
    switch ($key) {
      case 'exhibit_accepted_image_mimes': return $this->exhibit_model->fetchAcceptedImageMimes();
      case 'exhibit_formats': return $this->exhibit_model->fetchFormats();
      case 'exhibit_max_image_sizes':
      case 'exhibit_thumbnail_sizes': return $this->exhibit_model->fetchImageSizes($relative_key);
      case 'exhibit_max_upload_size': return $this->exhibit_model->fetchMaxUploadSize();
      default: return null;
    }
  }

  protected function willRegisterCSSAssets($assets)
  {
    $modifier = '';
    $structure = array(&$assets, &$modifier);
    if ($this->is_prod) {
      return $structure;
    }
    return $structure;
  }

  protected function willRegisterJSAssets($assets)
  {
    $modifier = '';
    $structure = array(&$assets, array('main' => &$modifier));
    if ($this->is_prod) {
      return $structure;
    }
    return $structure;
  }

  public function getURLs()
  {
    return array(
      // TODO: Use url generator.
    );
  }

  // Actions
  // -------

  public function indexAction()
  {
    return $this->app_view->render(array());
  }

  public function exhibitListAction()
  {
    $data = $this->exhibit_model->fetchIndexArray(ExhibitModel::INDEX_SECTIONAL);
    $data = $this->conventionallyTransformIndex($data);
    $headers = array();
    return new JsonResponse($data, 200, $headers);
  }

  public function exhibitAction(Application $app, Request $request, $id)
  {
    $headers = array();
    $params = json_decode($request->getContent(), true);
    switch ($request->getMethod()) {
      case 'GET':
        $data = $this->exhibit_model->fetchPostAssoc($id, 'id');
        $data = $this->conventionallyTransform($data, array(
          'omit_skipped' => false,
        ));
        return new JsonResponse($data, 200, $headers);
        break;
      case 'POST':
        // TODO: Handle.
        break;
      case 'PUT':
      case 'PATCH':
        $data = $this->conventionallyTransform($params, array(
          'reverse' => true,
        ));
        //var_dump($data); die;
        $did_update = $this->exhibit_model->updatePostAssoc($data);
        if ($did_update === false) {
          return new JsonResponse(array(
            'errors' => $this->exhibit_model->getValidationErrorJSON(),
          ), 400, $headers);
        }
        return new JsonResponse('success', 200, $headers);
        //return new JsonResponse($data, 200, $headers);
        break;
      case 'DELETE':
        // TODO: Handle.
        break;
      default: break;
    }
  }

  public function settingAction($type)
  {
    return '';
  }

}
