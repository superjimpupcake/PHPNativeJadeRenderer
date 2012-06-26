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
        $var_definitions = "";
        if(count($data) > 0){
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
}
