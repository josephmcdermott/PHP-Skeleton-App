{% extends layout_template_name %}
{% block content %}
  {% if errors %}
    <div class="alert alert-error">
      <h4>Error</h4>
      {% for single_error in errors %}
        <p>{{ single_error }}</p>
      {% endfor %}
    </div>
  {% endif %}
  {% if flash.message %}
    <div class="alert alert-info">
      <p>{{ flash.message }}</p>
    </div>
  {% else %}
    <div class="jumbotron">
      <h2>{{ page_title }}</h2>
      <form method="POST">
        {% include 'csrf_input.html' %}
        <div class="form-group">
          <label for="user_account_email">Your Email Address:</label>
          <input name="user_account_email" type="text" required="true" autofocus="autofocus" class="form-control">
        </div>
        <div class="form-group">
          <input class="btn btn-primary" type="submit" value="Submit">
        </div>
      </form>
    </div>
  {% endif %}
{% endblock %}
