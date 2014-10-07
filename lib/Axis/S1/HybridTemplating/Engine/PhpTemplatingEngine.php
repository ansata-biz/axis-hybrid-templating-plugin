<?php
/**
 * Date: 06.12.12
 * Time: 1:06
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\HybridTemplating\Engine;

use Axis\S1\HybridTemplating\Loader\BasicFilesystemLoader;

class PhpTemplatingEngine extends BaseTemplatingEngine
{
  protected $extension = 'php';
  /** @var BasicFilesystemLoader $loader */
  protected $loader;
  /** @var \sfContext $context */
  protected $context;

  function __construct($context)
  {
    $this->context = $context;
  }

  private function getLoader()
  {
    return $this->loader ?: $this->loader = $this->context->get('hybrid_templating.loader');
  }

  /**
   * @return bool
   */
  public function isEscapingNeeded()
  {
    return true;
  }

  public function render($module, $template, $vars = array())
  {
    /** @var BasicFilesystemLoader $loader */
    $loader = $this->getLoader();

    return $this->renderFile($loader->getTemplatePath($module, $template), $vars);
  }

  protected function renderFile($_sfFile, $vars = array())
  {
    extract($vars);

    // render
    ob_start();
    ob_implicit_flush(0);

    try
    {
      require($_sfFile);
    }
    catch (\Exception $e)
    {
      // need to end output buffering before throwing the exception #7596
      ob_end_clean();
      throw $e;
    }

    return ob_get_clean();
  }
}
