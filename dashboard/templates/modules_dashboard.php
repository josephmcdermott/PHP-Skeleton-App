{% extends layout_template_name %}
{% block content %}
  <div id="modules-module-menu">
  {% for module in default_module_list if module.menu_hidden != true %}
    <div class="col-sm-5 col-md-5 well well-lg single_module" title="{{ module.module_name }} Module: {{ module.module_description }}" onclick="window.location='/{{ module.handle }}'" onmouseover="this.style.cursor='pointer';">
      <img onclick="window.location='/{{ module.handle }}'" align="absmiddle" alt="{{ module.module_description }}" height="40" width="40" src="{{ module.module_icon_path }}">
          <a href="{{ module.path_to_this_module }}">{{ module.module_name }}</a>
          <p>{{ module.module_description }}</p>
    </div>
  {% endfor %}
  </div>
{% endblock %}
