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

        //check if the data is really changed
        $data_json = json_encode($data);

        $data_need_regen = true; 
        if(is_readable("$jade_template.data.md5")){
            $data_cache_md5 = file_get_contents("$jade_template.data.md5"); 
            if($data_cache_md5 == md5($data_json)){
                $data_need_regen = false; 
            }
        }

        if($data_need_regen){
            file_put_contents("$jade_template.data.md5", md5($data_json));
            file_put_contents("$jade_template.data", $data_json);
        }

        $jade_template_mtime = filemtime($jade_template);
        $jade_template_html_mtime = FALSE;
        if(is_readable("$jade_template.html")){
            $jade_template_html_mtime = filemtime("$jade_template.html");
        }

        if($data_need_regen || ($jade_template_mtime > $jade_template_html_mtime)){ //the jade template is modified, start the process of generating the html
            if(!isset($this->compiler_path)){
                $this->compiler_path = trim(shell_exec("which jade 2>&1")); //if the compiler path is not set yet, try to find a default one
            }

            if( basename($this->compiler_path) !== 'jade'){ //provide protection against arbitary command execution
                return "";
            }

            $var_definitions = "";
            $data = json_decode(file_get_contents("$jade_template.data"),true); //decode json as an associate array

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

            $jade_template_tmp = $jade_template.".tmp";

            file_put_contents($jade_template_tmp, $jade_template_content);

            shell_exec("{$this->compiler_path} -P < $jade_template_tmp > $jade_template.html");


            $output = file_get_contents("$jade_template.html");
            $output = trim($output);

            if($return_output){
                return $output;
            }
            else{
                print $output;
            }
        }
        else if(is_readable($jade_template.".html")){ //the jade template is not modified, do not start the process, simply just read the previous-generated html
            require $jade_template.".html";
        }
    }
}
