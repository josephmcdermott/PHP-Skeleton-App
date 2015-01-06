{% extends layout_template_name %}
{% block styles_head %}
  <style type="text/css">
    .jumbotron p {
      font-size: 16px;
    }
  </style>
{% endblock %}
{% block content %}
  <div class="jumbotron">
    <h2>Login</h2>
    {% if errors %}
        <div class="alert alert-warning">
          <h4>Invalid Username/Password</h4>
          <p>The email address or password that you have entered is invalid.  Please try again.</p>
        </div>
    {% endif %}
    <form method="POST">
      {% include 'csrf_input.html' %}
      <div class="form-group">
        <label for="user_account_email">User Name (email address):</label>
        <input name="user_account_email" type="text" autofocus="autofocus" class="form-control">
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input name="password" type="password" class="form-control">
      </div>
      <div class="form-group">
        <input class="btn btn-primary" type="submit" value="Login">
      </div>
    </form>
    {% if display_sign_up %}
      <p>Don't have an account? <a href="/user_account/register/" title="Register for a system account">Sign up</a></p>
    {% endif %}
    {% if display_password_reset %}
      <p>Forgot password? <a href="/user_account/password/" title="Reset your password">Reset</a></p>
    {% endif %}

  </div>
{% endblock %}
