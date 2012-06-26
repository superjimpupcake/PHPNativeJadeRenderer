PHPNativeJadeRenderer
=====================
Render Jade template with PHP using the native Jade Rendering Engine

## Usage:
1. Globally Install the jade template native compiler using npm install -g jade 
2. Write our main php file (assuming the jade is at /usr/local/bin/jade)

```php
<?php
require "src/PHPNativeJade/Renderer.php";

$renderer = new PHPNativeJade\Renderer();

$renderer->setNativeJadeCompiler("/usr/local/bin/jade");

$renderer->render("test.jade", array('items' => array(1,2,3,4,5)));
```

3. In test.jade we have

```jade
- if (items.length)
  ul
    - items.forEach(function(item){
      li= item
    - })
```

4. After the main php file is executed, we should see:

```html
<ul>
  <li>1</li>
  <li>2</li>
  <li>3</li>
  <li>4</li>
  <li>5</li>
</ul>
```
