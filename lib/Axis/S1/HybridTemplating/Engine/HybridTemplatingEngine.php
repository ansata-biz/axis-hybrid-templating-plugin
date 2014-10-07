<?php
/**
 * Date: 06.12.12
 * Time: 4:27
 * Author: Ivan Voskoboynyk
 */
namespace Axis\S1\HybridTemplating\Engine;

use Axis\S1\HybridTemplating\Loader\BasicFilesystemLoader;

class HybridTemplatingEngine extends BaseTemplatingEngine
{
  /**
   * @var string
   */
  protected $extension = '~'; // should not be used

  /**
   * @var array|TemplatingEngine[]
   */
  protected $engines = array();

  /**
   * @var \sfEventDispatcher
   */
  protected $dispatcher;

  /**
   * @var BasicFilesystemLoader
   */
  protected $loader;

  /**
   * @param array|TemplatingEngine[] $engines
   * @param BasicFilesystemLoader $loader
   * @param \sfEventDispatcher $dispatcher
   */
  public function __construct($engines = array(), $loader, $dispatcher)
  {
    foreach ($engines as $engine)
    {
      $this->engines[$engine->getExtension()] = $engine;
    }
    $this->dispatcher = $dispatcher;
    $this->loader = $loader;

  }

  public function isEscapingNeeded()
  {
    return false;
  }

  /**
   * @param string $template
   * @param string $module
   * @param array $vars
   * @return string
   *
   * @throws \InvalidArgumentException If template format is not supported
   */
  public function render($module, $template, $vars = array())
  {
    $path = $this->loader->getTemplatePath($module, $template);
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!isset($this->engines[$ext]))
    {
      throw new \InvalidArgumentException(sprintf(
        'Template format "%s" of file "%s" is not supported.',
        $ext,
        $template
      ));
    }

    $engine = $this->engines[$ext];

    // escape variables
    if ($engine->isEscapingNeeded())
    {
      $vars = $this->initializeAttributeHolder($vars)->toArray();
    }

    return $engine->render($module, $template, $vars);
  }

  /**
   * @param $vars
   * @return \sfViewParameterHolder
   */
  protected function initializeAttributeHolder($vars)
  {
    return new \sfViewParameterHolder($this->dispatcher, $vars, array(
      'escaping_method'   => \sfConfig::get('sf_escaping_method'),
      'escaping_strategy' => \sfConfig::get('sf_escaping_strategy'),
    ));
  }
}
