{% extends layout_template_name %}
{% block content %}
  <div class="jumbotron">
    <h2><i class="glyphicon glyphicon-import"></i> {{ page_title }}</h2>
    <p>This "quickstart" installer writes key information to the "default_global_settings.php" file and creates the baseline database tables and content supporting users and groups. Additionally, it creates the first "Universal Administrator".</p>
  </div>
  {% if errors %}
    <div class="alert alert-danger">
      <h4>Error</h4>
      {% for single_error in errors %}
        <p>{{ single_error }}</p>
      {% endfor %}
    </div>
  {% endif %}
  {% if data.success_message %}
    <div class="alert alert-success">
      <h4>All Set!</h4>
      <p>The installation was successful. You can <a href="/authenticate/">login here</a>.</p>
    </div>
  {% endif %}
  {% if data.error_message %}
    <div class="alert alert-danger">
      <h4>Error</h4>
      <p>The installation failed.</p>
    </div>
  {% endif %}
  {% if flash.message %}
    <div class="alert alert-danger">
      <p>{{ flash.message }}</p>
    </div>
  {% endif %}

  {% if data.success_message is empty %}
    <p style="margin:20px 0 20px 20px;"><i class="glyphicon glyphicon-info-sign"></i> All fields are required.</p>
    <p style="margin:20px 0 20px 20px;"><i class="glyphicon glyphicon-cog"></i> <strong>Difficulties?</strong> <a href="/webapp_installer/library/env.php">Run the Environment Check</a></p>
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
        <div class="form-group">
          <label for="first_name">Your First Name:</label>
          <input name="first_name" type="text" value="{{ data.first_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="last_name">Your Last Name:</label>
          <input name="last_name" type="text" value="{{ data.last_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="application_name">Application Name:</label>
          <input name="application_name" type="text" value="{{ data.application_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="session_key">Session Key:</label>
          <input name="session_key" type="text" value="{{ data.session_key|e }}" required="true" placeholder="yourappname_session_key" class="form-control">
        </div>
      </div>
      <div class="col-sm-6 col-md-6" style="padding-left:20px;">
        <div class="form-group">
          <label for="cname">CNAME (URL):</label>
          <input name="cname" type="text" required="true" value="{{ data.cname|e }}" class="form-control">
        </div>
        {# <div class="form-group">
          <label for="http_mode">HTTP/HTTPS:</label>
          <input name="http_mode" type="text" value="{{ data.http_mode|e }}" required="true" class="form-control">
        </div> #}
        <div class="form-group">
          <label for="database_host">Database Host</label>
          <input name="database_host" type="text" value="{{ data.database_host|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="database_name">Database Name</label>
          <input name="database_name" type="text" value="{{ data.database_name|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="database_username">Database Username</label>
          <input name="database_username" type="text" value="{{ data.database_username|e }}" required="true" class="form-control">
        </div>
        <div class="form-group">
          <label for="database_password">Database Password</label>
          <input name="database_password" type="text" value="{{ data.database_password|e }}" required="true" class="form-control">
        </div>
        {# <div class="form-group">
          <label for="file_upload_location">File Upload Location:</label>
          <input name="file_upload_location" type="text" value="{{ data.file_upload_location|e }}" required="true" placeholder="/path/to/files" class="form-control">
        </div> #}
        <div class="form-group">
          <input class="btn btn-primary pull-right" type="submit" value="Run the Installer">
        </div>
      </div>
    </form>
  {% else %}
    <h4>Results</h4>
    {% for single_data in data %}
      <p><span class="glyphicon glyphicon-ok"></span> {{ single_data|e }}</p>
    {% endfor %}
  {% endif %}
{% endblock %}
