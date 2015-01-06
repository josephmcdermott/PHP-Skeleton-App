{% extends layout_template_name %}
{% block content %}
  <div class="jumbotron">
    <h2>{{ page_title }}</h2>
    <p>Register for a system account.</p>
  </div>
  {% if errors %}
    <div class="alert alert-danger">
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
    <p style="margin:20px 0 20px 20px;">All fields are required.</p>
    <form method="POST">
      {% include 'csrf_input.html' %}
      <div class="col-sm-6 col-md-6" style="padding-left:20px;">
        <div class="form-group">
          <label for="user_account_email">Your Email Address:</label>
          <input name="user_account_email" type="text" value="{{ data.user_account_email|e }}" required="true" autofocus="autofocus" class="form-control">
        </div>
        <div class="form-group">
          <label for="user_account_password">Your Desired Password:</label>
          <input name="user_account_password" type="password" required="true" class="form-control">
        </div>
      </div>
      <div class="col-sm-6 col-md-6" style="padding-left:20px;">
        <div class="form-group">
          <label for="first_name">Your First Name:</label>
          <input name="first_name" type="text" value="{{ data.first_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="last_name">Your Last Name:</label>
          <input name="last_name" type="text" value="{{ data.last_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <input class="btn btn-primary pull-right" type="submit" value="Submit">
        </div>
      </div>
    </form>
  {% endif %}
{% endblock %}
