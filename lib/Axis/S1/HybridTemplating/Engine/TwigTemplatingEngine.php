<?php
/**
 * Date: 06.12.12
 * Time: 3:59
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\HybridTemplating\Engine;

class TwigTemplatingEngine extends BaseTemplatingEngine
{
  /** @var \Twig_Environment */
  protected $twig;

  /**
   * @param \Twig_Environment $twig
   * @param string $extension
   */
  function __construct($twig, $extension = 'twig')
  {
    $this->twig = $twig;
    parent::__construct($extension);
  }

  /**
   * @param string $template
   * @param array $vars
   * @return string
   */
  public function render($template, $vars = array(), $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE)
  {
    /** @var $loader \Twig_Loader_Filesystem */
    $loader = $this->twig->getLoader();
    $loader->addPath(realpath(dirname($template)), $namespace);

    return $this->twig->render('@' . $namespace . '/' . basename($template), $vars);
  }

  public function isEscapingNeeded()
  {
    return false;
  }
}
