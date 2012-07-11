<?php
namespace PHPNativeJade;

class Renderer
{
  private $compiler_path;

  public function setNativeJadeCompiler($compiler_path)
  {
    $this->compiler_path = $compiler_path;
  }

  public function render($jade_template, $data = array(), $return_output = false)
  {

    //make the cache directory, the first time
    $jade_template_basename = basename($jade_template);
    $cache_dir = dirname($jade_template)."/.jadecache";
    $data_cache = $cache_dir."/$jade_template_basename.data";
    $data_cache_md5 = $cache_dir."/$jade_template_basename.data.md5";
    $template_jade_tmp = $cache_dir."/$jade_template_basename.tmp";
    $template_cache_html = $cache_dir."/$jade_template_basename.html";

    if(!is_readable($cache_dir)){
      $cache_dir_created = mkdir($cache_dir);
      if(!$cache_dir_created){
        return "";
      }
    } 

    //check if the data is really changed
    $data_json = json_encode($data);

    $data_need_regen = true; 
    if(is_readable($data_cache_md5)){
      $data_cache_md5_content = file_get_contents($data_cache_md5); 
      if($data_cache_md5_content == md5($data_json)){
        $data_need_regen = false; 
      }
    }

    if($data_need_regen){
      file_put_contents($data_cache_md5, md5($data_json));
      file_put_contents($data_cache, $data_json);
    }

    $jade_template_mtime = filemtime($jade_template);
    $jade_template_html_mtime = FALSE;
    if(is_readable($template_cache_html)){
      $jade_template_html_mtime = filemtime($template_cache_html);
    }

    if($data_need_regen || ($jade_template_mtime > $jade_template_html_mtime)){ //the jade template is modified, start the process of generating the html
      if(!isset($this->compiler_path)){
        system("which jade 2>&1", $compiler_path); //if the compiler path is not set yet, try to find a default one
        $this->compiler_path = $compiler_path;
      }

      if( basename($this->compiler_path) !== 'jade'){ //provide protection against arbitary command execution
        return "";
      }

      $var_definitions = "";
      $data = json_decode(file_get_contents($data_cache),true); //decode json as an associate array

      if(is_array($data) && count($data) > 0){
        foreach($data as $var_name => $value){
          if(is_array($value)){
            $value = json_encode($value);
          }
          else if(is_string($value)){
            $value = '"'.addslashes($value).'"';
          }
          $var_definitions .= "- var $var_name = $value\n";
        }
      }

      $jade_template_content = file_get_contents($jade_template);        
      $jade_template_content = $var_definitions . $jade_template_content;

      file_put_contents($template_jade_tmp, $jade_template_content);

      system("{$this->compiler_path} -P < $template_jade_tmp > $template_cache_html 2>&1");


      $output = file_get_contents($template_cache_html);
      $output = trim($output);

      if($return_output){
        return $output;
      }
      else{
        print $output;
      }
    }
    else if(is_readable($template_cache_html)){ //the jade template is not modified, do not start the process, simply just read the previous-generated html
      require $template_cache_html;
    }
  }
}
