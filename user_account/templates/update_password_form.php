{% extends layout_template_name %}
{% block content %}
  <div class="jumbotron">
    <h2>{{ page_title }}</h2>
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
    {% endif %}
      <form method="POST">
        {% include 'csrf_input.html' %}
        <div class="form-group">
          <label class="control-label" for="user_account_password">Change Password:</label>
          <div class="controls">
            <input name="user_account_password" id="user_account_password" type="password" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="password_check">Retype New Password</label>
          <div class="controls">
            <input name="password_check" id="password_check" type="password" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <input name="user_account_email" id="user_account_email" type="hidden" value="{{ data.user_account_email }}" class="form-control">
          <input name="emailed_hash" id="emailed_hash" type="hidden" value="{{ data.emailed_hash }}" class="form-control">
          <input class="btn btn-primary" type="submit" value="Submit">
        </div>
      </form>
  </div>
{% endblock %}
