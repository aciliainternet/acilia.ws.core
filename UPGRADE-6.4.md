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

* Tabs usage

    *Before*

    ```html
    <li class="l-content__tab-item is-active" data-tab="main" data-tabsection="tab-content">
        tab1
    </li>

    <div class="l-content__tab-content" id="main" data-tablink="tab-content">
        content tab1
    </div>
    ```

   *After*

    ```html
    <li class="l-content__tab-item is-active" data-action="click->ws-tabs#onTabClick" data-ws-tabs-target="tab">
        tab1
    </li>

    <div class="l-content__tab-content" id="main" data-ws-tabs-target="tabPanel">
        content tab1
    </div>
   ```

* Button Delete

*Before*

```html
{% block crud_table_body_row_actions %}
    <td class="c-table__cell">
        {% block crud_table_body_row_action %}{% endblock %}
        {% if is_granted(view_roles['delete']) %}
            <button class="c-btn c-btn--secondary c-btn--delete js-genericDelete" data-id="{{ entity.id }}" data-url="{{ ws_cms_path("#{route_prefix}_delete_client_group_product", { 'uuid': group.uuid, 'product': entity.id }) }}" data-title="{{ 'title'|trans([], trans_prefix) }}" data-message="{{ 'delete_warning'|trans([], trans_prefix) }}">

                <i class="fal fa-trash-alt"></i>
            </button>
        {% endif %}
    </td>
{% endblock %}
```

 *After*

 ```html
 ### Add new block with new attributes
 {% block crud_table_header_row_data %}
    data-controller="ws-generic-delete"
    data-ws-generic-delete-id-value="{{ entity.id }}"
    data-ws-generic-delete-url-value="{{ ws_cms_path("#{route_prefix}_delete_client_group_product", { 'uuid': group.uuid, 'product': entity.id }) }}"
    data-ws-generic-delete-title-value="{{ 'title'|trans([], trans_prefix) }}"
    data-ws-generic-delete-message-value="{{ 'delete_warning'|trans([], trans_prefix) }}"
{% endblock crud_table_header_row_data %}
 ```
