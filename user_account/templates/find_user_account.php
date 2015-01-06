{% extends layout_template_name %}
{% block content %}
  <div class="col-sm-6 col-md-6">
    <form id="user_account_search" class="form-horizontal" method="POST">
        <div class="form-group">
          <label class="control-label" for="user"><span style="color:red;">*</span>Name:</label>
          <div class="controls">
            <input role="typeahead" size="50" id="user" type="text" data-typeahead-target="client_id" class="form-control" autofocus="autofocus">
            <input type="hidden" id="client_id" name="client-id" class="form-control">
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
  <script src="//cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){

      var users = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: '{{ path_to_this_module }}/find/?q=abc',
        remote: '{{ path_to_this_module }}/find/?q=%QUERY'
      });
       
      users.initialize();
       
      $('#user').typeahead({
        hint: true,
        highlight: true,
        minLength: 3,
        items: 10
      },
      {
        name: 'users',
        displayKey: 'displayname',
        source: users.ttAdapter()
      });

      $("#user").on("typeahead:selected typeahead:autocompleted", function(e, datum) {
        $("#client_id").val( datum.user_account_id );
      });

      $("#user_account_search").submit(function(event){
        event.preventDefault();
        window.location.href = '{{ path_to_this_module }}/manage/' + $("#client_id").val();
      });
    });
  </script>
{% endblock %}
