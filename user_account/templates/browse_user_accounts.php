{% extends layout_template_name %}
{% block styles_head %}
	{{ parent() }}
	<link href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="/{{ core_type }}/lib/javascripts/DataTables-extended/css/bootstrap_datatables.css" rel="stylesheet" type="text/css" />
	<link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/0.3.2/sweet-alert.min.css" rel="stylesheet" type="text/css">
{% endblock %}
{% block content %}
  {% if flash.message %}
    <div class="alert alert-success" role="alert">
      <p>{{ flash.message }}</p>
    </div>
  {% endif %}
	<table id="browse" class="table table-striped table-bordered" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="text-center">Manage</th>
      <th class="text-center">Name</th>
      <th class="text-center">Group</th>
      <th class="text-center">Active</th>
    </tr>
  </thead>
</table>
{% endblock %}
{% block js_bottom %}
	{{ parent() }}
	<script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/0.3.2/sweet-alert.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
	    $('#browse').dataTable( {
					"columns": [
							{ "data": "manage" },
							{ "data": "name" },
							{ "data": "groups" },
							{ "data": "active" }
						],
					"dom": '<"wrapper"fli><"col-sm-3 col-md-3 datatables_bulk_actions">pt<"bottom"ip><"clear">',
					"pagingType": "simple_numbers",
					"stateSave": true,
					"order": [[1,"desc"]],
					// Show processing throbber.
        	"processing": true,
					"serverMethod": "POST",
	        // All data management will be done on the server side.
        	"serverSide": true,
        	// Path to the file that is going to handle the queries.
        	"ajax": "{{ path_to_this_module }}/datatables_browse_user_accounts",
        	// Method type.
        	"serverMethod": "POST",
        	// Values in the length dropdown.
        	"lengthMenu":[10,50,100,500],
        	// Set some widths.
        	"aoColumnDefs":[
           		{"sWidth":"100px","aTargets":[0]}
           		,{"bSortable":false,"aTargets":[0]}
        	],
					"fnRowCallback":function(nRow, aData, iDisplayIndex) {
        		// Create the checkboxes.
        		$(nRow).find('td:eq(0)').html(
	        		"<input type='checkbox' name='manage_checkbox' value='" + aData['manage'] + "' />"
	        	)
	        	.addClass("manage_column");
	        }
				});

			// Send to details page when clicked.
			var details_page = "{{ path_to_this_module }}/manage/";
	    $('#browse tbody').on('click','td',function(event){
	    	if(!$(this).hasClass("manage_column")){
		    	var user_id = $(this).closest("tr").attr('id');
		    	window.location.href = details_page + user_id;
	    	}
	    });

	    var delete_button = $("<div></div>")
	    	.addClass("delete")
	    	.on("click",function(){

	    		var delete_ids = new Array;
	    		$('#browse [name="manage_checkbox"]:checked').each(function(){
	    			delete_ids.push($(this).val());
	    		});

	    		if(delete_ids.length > 0) {

	    			swal({
		          title: "Confirm",
		          text: "Are you sure you want to delete the selected item(s)?",
		          type: "warning",
		          showCancelButton: true,
		          confirmButtonColor: "#DD6B55",
		          confirmButtonText: "Yes, delete the item(s).",
		          closeOnConfirm: true
		        },
		        function(){
		    			$.ajax({
						    type:"POST"
						    ,dateType:"json"
						    ,url: "{{ path_to_this_module }}/delete"
						    ,data: ({id: JSON.stringify(delete_ids), csrf_key: "{{ csrf_token }}"})
						    ,success: function(ajax_return){
						    	$('#browse').dataTable().fnDraw();
						    }
							});
	    			});

	    		}
	    	});

	    $(".datatables_bulk_actions").append(delete_button);

		});
	</script>
{% endblock %}
