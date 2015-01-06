{% extends layout_template_name %}
{% block content %}
  <div class="jumbotron">
    <h2>{{ page_title }}</h2>
  </div>
  {% if message.success %}
    <div class="alert alert-success">
      <p>{{ message.success }}</p>
    </div>
    <p>You can <a href="/authenticate/">login here</a>.</p>
  {% else %}
    <div class="alert alert-danger">
      <p>{{ message.failed }}</p>
    </div>
  {% endif %}
{% endblock %}
