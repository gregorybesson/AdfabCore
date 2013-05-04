<?php

namespace AdfabCore\View\Helper;

use Zend\View\Helper\AbstractHelper;

class AdCKEditor extends AbstractHelper
{

    /**
     * @var
     */
    protected $Config;

    /**
     * @param $Config
     */
    public function __construct($Config)
    {
       $this->Config = $Config;
    }

    /**
     * @param $name
     * @param $options
     *
     * @return string
     */
    public function __invoke($name,$options)
    {
        return $this->AdCKEditor($name,$options);
    }

    /**
     * @param $name
     * @param $options
     *
     * @return string
     */
    public function AdCKEditor($name,$options = array())
    {
        $CKEditor = new \AdfabCore\Service\CKEditor();

        $CKEditor->returnOutput = true;

        /*
         * General module configurations
         */
        if(isset($this->Config['BasePath']))	$CKEditor->basePath 			= $this->Config['BasePath'].'/';
        if(isset($this->Config['Toolbar']))		$CKEditor->config['toolbar']    = $this->Config['Toolbar'];
        if(isset($this->Config['Width']))       $CKEditor->config['width']      = $this->Config['Width'];
        if(isset($this->Config['Height']))      $CKEditor->config['height']     = $this->Config['Height'];
        if(isset($this->Config['Language']))    $CKEditor->config['language']   = $this->Config['Language'];
        if(isset($this->Config['Color']))       $CKEditor->config['uiColor']    = $this->Config['Color'];
        if(isset($this->Config['stylesSet']))   $CKEditor->config['stylesSet']  = $this->Config['stylesSet'];
		if(isset($this->Config['contentsCss'])) $CKEditor->config['contentsCss']= $this->Config['contentsCss'];
        if(isset($this->Config['templates_files']))   $CKEditor->config['templates_files']  = $this->Config['templates_files'];

        // El Finder
        if(isset($this->Config['ElFinderBaseURL']))      $CKEditor->config['filebrowserBrowseUrl']    = $this->Config['ElFinderBaseURL'];
        if(isset($this->Config['ElFinderWindowWidth']))  $CKEditor->config['filebrowserWindowWidth']  = $this->Config['ElFinderWindowWidth'];
        if(isset($this->Config['ElFinderWindowHeight'])) $CKEditor->config['filebrowserWindowHeight'] = $this->Config['ElFinderWindowHeight'];

        /*
         * special confirmations in your form
         */
        if(isset($options['BasePath']))	 $CKEditor->basePath		    = $options['BasePath'].'/';
        if(isset($options['Toolbar']))   $CKEditor->config['toolbar']   = $options['Toolbar'];
        if(isset($options['toolbar']))   $CKEditor->config['toolbar']   = $options['toolbar'];
        if(isset($options['Width']))     $CKEditor->config['width']     = $options['Width'];
        if(isset($options['Height']))    $CKEditor->config['height']    = $options['Height'];
        if(isset($options['Language']))  $CKEditor->config['language']  = $options['Language'];
        if(isset($options['Color']))     $CKEditor->config['uiColor']   = $options['uiColor'];
        if(isset($options['stylesSet'])) $CKEditor->config['stylesSet'] = $options['stylesSet'];
        if(isset($options['contentsCss'])) $CKEditor->config['contentsCss'] = $options['contentsCss'];
        if(isset($options['templates_files']))   $CKEditor->config['templates_files']  = $options['templates_files'];

        // El Finder
        if(isset($options['ElFinderBaseURL']))           $CKEditor->config['filebrowserBrowseUrl']    = $options['ElFinderBaseURL'];
        if(isset($options['ElFinderWindowWidth']))       $CKEditor->config['filebrowserWindowWidth']  = $options['ElFinderWindowWidth'];
        if(isset($options['ElFinderWindowHeight']))      $CKEditor->config['filebrowserWindowHeight'] = $options['ElFinderWindowHeight'];

        echo $CKEditor->replace($name);
    }
}
