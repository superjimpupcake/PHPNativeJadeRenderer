PHPNativeJadeRenderer
=====================
Render Jade template with PHP using the native Jade Rendering Engine

## Usage:
1.Globally Install the jade template native compiler using npm install -g jade 

  For more details on npm, please see: http://npmjs.org/

2.Write our main php file (assuming the jade compiler is at /usr/local/bin/jade and PHP can run /usr/local/bin/jade with shell_exec function properly)

For detail usage on Jade Template, please see: https://github.com/visionmedia/jade#readme

```php
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
```

3.In test.jade we have

```jade
- if (items.length)
  ul
    - items.forEach(function(item){
      li= item
    - })
for user in students 
  if user.role == 'admin'
    p #{user.name} is an admin
  else
    p= user.name
p= content
p!= content
```

4.After the main php file is executed, we should see:

```html
<ul>
  <li>1</li>
  <li>2</li>
  <li>3</li>
  <li>4</li>
  <li>5</li>
</ul>
<p>tom</p>
<p>ken is an admin</p>
<p>john</p>
<p>This is a paragraph from the cms &lt;br/&gt;</p>
<p>This is a paragraph from the cms <br/></p>
```
