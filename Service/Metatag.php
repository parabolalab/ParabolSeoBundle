<?php

namespace Parabol\SeoBundle\Service;

/**
* Metatag
*/
class Metatag extends \Twig_Extension
{
		
	private $metatags;

	public function getName()
	{
		return 'metatag';
	}

	public function getFunctions()
	{
	    return array(
	        new \Twig_SimpleFunction('renderMetas', [$this, 'render'], [
	            'is_safe' => ['html']
	            ]
	        )
	    );
	}

  public function renderTagAttr($attr)
  {
      $results = '';
      foreach((array)$attr as $name => $value)
      {
          $results .= ' ' . $name . '="' . $value . '"';
      }
      return $results;
  }

	public function render()
	{
		$code = '';

    foreach((array)$this->metatags as $name => $tag)
		{
			switch($name)
			{
        case 'link':
          $code .= '<link '.$this->renderTagAttr($tag['value']).' />';
          break;
				case 'title':
					$code .= '<title>'.$tag['value'].'</title>';
        default:
					$code .= '<meta property="'.$name.'" name="'.$name.'" content="'.$tag['value'].'">';
			}

      

			$code .= "\n";
		}


		return $code;
	}

	public function addMetatags($tags, $url, $joinWith = ' - ', $overwrite = false)
	{
		foreach($tags as $name => $tag)
		{
			if($this->getMetatagUrl($name) != $url) 
			{
				if(isset($tag['join']) && $tag['join'] && $this->getMetatagValue($name)) $tag['value'] .= $joinWith . $this->getMetatagValue($name);
				$this->addMetatag($name, $tag['value'], $url, $overwrite);
			}
		}

	}

	public function addMetatag($name, $value, $url = null, $overwrite = false)
    {
    	if($value)
    	{
        $url = preg_replace('/^\/app_dev.php/', '', $url);
        if(!isset($this->metatags[$name]) || $overwrite || strlen($this->metatags[$name]['url']) < strlen($url)) $this->metatags[$name] = ['value' => $value, 'url' => $url];
    	} 
    }

   	public function hasMetatag($name)
    {
    	return array_key_exists($name, $this->metatags);
    }

    public function getMetatagValue($name)
    {
    	return $this->hasMetatag($name) ?  $this->metatags[$name]['value'] : null;
    }

    public function getMetatagUrl($name)
    {
    	return $this->hasMetatag($name) ? $this->metatags[$name]['url'] : null;
    }
}