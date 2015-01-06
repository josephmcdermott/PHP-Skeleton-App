{% extends layout_template_name %}
{% block content %}
	<div class="col-sm-6 col-md-6">
		{% if errors %}
			<div class="alert alert-danger">
				<h4>Form Errors</h4>
				{% for single_error in errors %}
				<p>{{ single_error }}</p>
				{% endfor %}
			</div>
		{% endif %}
		<form method="POST" class="form-horizontal">
			{% include 'csrf_input.html' %}
    	<div class="form-group">
	    	<label class="control-label" for="name"><span style="color:red;">*</span>Name:</label>
	    	<div class="controls">
	    		<input name="name" id="name" type="text" value="{{ group_data.name|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="abbreviation"><span style="color:red;">*</span>Abbreviation:</label>
	    	<div class="controls">
	    		<input name="abbreviation" id="abbreviation" type="text" value="{{ group_data.abbreviation|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="description">Description:</label>
	    	<div class="controls">
	    		<textarea name="description" rows="5" cols="50" class="form-control">{{ group_data.description|e }}</textarea>
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="address_1">Address 1:</label>
	    	<div class="controls">
	    		<input name="address_1" id="address_1" type="text" value="{{ group_data.address_1|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="address_2">Address 2:</label>
	    	<div class="controls">
	    		<input name="address_2" id="address_2" type="text" value="{{ group_data.address_2|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="city">City:</label>
	    	<div class="controls">
	    		<input name="city" id="city" type="text" value="{{ group_data.city|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="state">State:</label>
	    	<div class="controls">
	    		<input name="state" id="state" type="text" value="{{ group_data.state|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="zip">Zip Code:</label>
	    	<div class="controls">
	    		<input name="zip" id="zip" type="text" value="{{ group_data.zip|e }}" class="form-control">
    		</div>
    	</div>
    	<div class="form-group">
	    	<label class="control-label" for="group_parent">Direct Parent Group:</label>
    		<div class="controls">
	    		<select id="group_parent" name="group_parent" size="15" class="form-control">
	    			{% for single_group in groups %}
	    				{% if single_group.group_id != group_data.group_id %}
		    				<option {% if single_group.group_id == group_data.group_parent %} selected='selected' {% endif %} value="{{ single_group.group_id }}">{{ single_group.indent }}{{ single_group.name }} ({{ single_group.abbreviation }})</option>
	    				{% endif %}
	    			{% endfor %}
	    		</select>
	    	</div>
    	</div>

    	<div class="form-group">
    		<input class="btn btn-primary" type="submit" value="Submit">
    	</div>
    </form>
	</div>
{% endblock %}
{% block js_bottom %}
	{{ parent() }}
	<script type="text/javascript">
    	$(document).ready(function(){
    		$("#group_parent").on("click",function(event){
    			if(event.ctrlKey){
    				$(this).find(":selected").removeAttr("selected");
    			}
    		});
    	});
    </script>
{% endblock %}
