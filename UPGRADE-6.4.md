UPGRADE FROM 6.3 to 6.4
=======================

Widestand 6.4 introduces the Symfony 6.4 veersion and Node 20 as the js engine. It also add typescript and Stimulus for the CMS Javascript.

Table of Contents
-----------------
* [FormTypes](#FormTypes)

FormTypes
---------------

* SlugType change data-componet for data-controller
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

* SelectType change data-componet for data-controller
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

* DatePickerType and DateTimePickerType change data-componet for data-controller
    *Before*

   ```php
   'attr' => [
        'data-component' => 'ws_datepicker',
    ],
   ```

   *After*

   ```php
   'attr' => [
        'data-controller' => 'ws-datepicker',
    ],
   ```

* InputMultipleType change data-componet for data-controller
    *Before*

   ```php
   'attr' => [
        'data-component' => 'ws_input-multiple',
    ],
   ```

   *After*

   ```php
   'attr' => [
        'data-controller' => 'ws-input-multiple',
    ],
   ```

* Dropdowns change data-componet for data-controller
    *Before*

   ```html
    data-component="ws_dropdown"
   ```

   *After*

   ```html
    'data-controller' => 'ws-dropdown',
   ```

* Table collapse change data-componet for data-controller
    *Before*

    ```html
    data-component="ws_table_collapse"
    ```

   *After*

    ```html
    'data-controller' => 'ws-table-collapse',
   ```

* Character count usage

    ```php
        ->add('name', null, [
            'attr' => [
                'placeholder' => 'form.name.placeholder',
                'data-character-count-target' => 'field',
                'data-action' => 'keyup->character-count#change',
            ],
            'help' => '<span class="js-count">0</span> out of <span class="js-maxCount">60</span> characters recommended',
            'help_html' => true,
            'row_attr' => [
                'data-controller' => 'character-count',
                'data-character-count-max-value' => 60
            ]
        ])
    ```
