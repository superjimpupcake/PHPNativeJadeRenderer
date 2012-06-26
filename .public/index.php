<?php
require "../src/PHPNativeJade/Renderer.php";

$renderer = new PHPNativeJade\Renderer();
$renderer->setNativeJadeCompiler("/usr/local/bin/jade");
$renderer->render("index.jade", array(
    'items' => array(1,2,3,4,5),
    'students' => array(
        array('name' => 'tom', 'role' => 'editor'),
        array('name' => 'ken', 'role' => 'admin'),
        array('name' => 'john', 'role' => 'visitor')
    ),
    'content' => 'This is a paragraph from the cms <br/>',
));
