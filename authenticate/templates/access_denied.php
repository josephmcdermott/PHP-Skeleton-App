{% extends layout_template_name %}
{% block content %}
	<div class="jumbotron">
		<h2>Access Denied</h2>
	</div>
	{% if flash.message %}
		<div class="alert alert-danger">
			<p>{{ flash.message }}</p>
		</div>
	{% else %}
		<div class="alert alert-danger">
				<p>You do not have sufficient privledges to view this page.</p>
		</div>
	{% endif %}
{% endblock %}
