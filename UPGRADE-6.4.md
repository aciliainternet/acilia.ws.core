UPGRADE FROM 6.3 to 6.4
=======================

Widestand 6.4 introduces the Symfony 6.4 veersion and Node 20 as the js engine. It also add typescript and Stimulus for the CMS Javascript.

Table of Contents
-----------------
* [FormTypes](#FormTypes)

FormTypes
---------------

* SlugType chnage data-componet for data-controller
    *Before*

   ```php
   'attr' => [
        'data-component' => 'ws_slug',
    ],
   ```

   *After*

   ```php
   'attr' => [
        'data-controller' => 'ws-slug',
    ],
   ```

* SelectType chnage data-componet for data-controller
    *Before*

   ```php
   'attr' => [
        'data-component' => 'ws_select',
    ],
   ```

   *After*

   ```php
   'attr' => [
        'data-controller' => 'ws-select',
    ],
   ```
