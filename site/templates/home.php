{% extends 'bootstrap_home_page.php' %}
{% block styles_head %}
  {{ parent() }}
{% endblock %}
{% block content %}
  <div class="row-fluid">
    {% if flash.message %}
      <div class="alert alert-info">
        <p>{{ flash.message }}</p>
      </div>
    {% endif %}

    {% if errors %}
      <div class="alert alert-danger">
        <h4>Form Errors</h4>
        {% for single_error in errors %}
        <p>{{ single_error }}</p>
        {% endfor %}
      </div>
    {% endif %}

    <!-- <p>This is the home page in the "site" module. <a href="/subpage/">Go to the subpage</a>.</p> -->

  </div>
{% endblock %}
{% block js_bottom %}
  {{ parent() }}
  <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script> -->
{% endblock %}