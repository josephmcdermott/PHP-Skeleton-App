{% extends layout_template_name %}
{% block content %}
	<div class="jumbotron">
		<h2>Access Denied</h2>
		{% if flash.message %}
				<div class="alert alert-warning">
					<p>{{ flash.message }}</p>
				</div>
		{% else %}
			<div class="alert alert-warning">
					<p>You do not have sufficient privledges to view this page.  Please contact your administrator.</p>
			</div>
		{% endif %} 
	</div>
{% endblock %}