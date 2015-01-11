{% extends layout_template_name %}
{% block styles_head %}
  {{ parent() }}
  <link href="//cdnjs.cloudflare.com/ajax/libs/chosen/0.9.11/chosen.css" rel="stylesheet" type="text/css" />
  <link href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/0.3.2/sweet-alert.min.css" rel="stylesheet" type="text/css">
{% endblock %}
{% block content %}
    {% if flash.message %}
      <div class="alert alert-info">
        <p>{{ flash.message }}</p>
      </div>
    {% endif %}

    <h3>{{ current_user_account_info.first_name }} {{ current_user_account_info.last_name }}</h3>

    {% if errors %}
      <div class="alert alert-danger">
        <h4>Form Errors</h4>
        {% for single_error in errors %}
        <p>{{ single_error }}</p>
        {% endfor %}
      </div>
    {% endif %}

    <form id="user_account_form" method="POST">
      {% include 'csrf_input.html' %}
      <div class="row">
        <!-- Column 1 -->
        <div class="col-sm-6 col-md-6">
          <div class="form-group">
            <label class="control-label" for="first_name"><span style="color:red;">*</span>First Name:</label>
            <div class="controls">
              <input name="first_name" id="first_name" type="text" required="true" value="{{ account_info.first_name|e }}" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="last_name"><span style="color:red;">*</span>Last Name:</label>
            <div class="controls">
              <input name="last_name" id="last_name" type="text" required="true" value="{{ account_info.last_name|e }}" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="user_account_email"><span style="color:red;">*</span>Email Address:</label>
            <div class="controls">
              <input name="user_account_email" id="user_account_email" required="true" type="text" value="{{ account_info.user_account_email|e }}" class="form-control">
            </div>
          </div>
        </div>
        <!-- Column 2 -->
        <div class="col-sm-6 col-md-6">
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
        </div>
      </div>

      <h4>Manage Addresses</h4>

      <div id="user_address_div">
      </div>

      <input name="address_count" id="address_count" type="hidden" value="0" />

      <button type="button" name="add" class="btn btn-info add_user_address btn-sm" title="Add an Address" id="user_address_button">
        <i class="glyphicon glyphicon-plus"></i> Add an Address
      </button>

      {% if role_perm_modify_own_groups %}
        <hr style="clear:both;">

        <h4>Manage Groups</h4>
          <div class="field-group add_group">
              <label for="group">Add a Group:</label>
              <div class="field">
                <select id="group" name="group">
                  <option value="0">Select...</option>
                  {% for single_group in groups %}
                    <option {% if single_group.admin == false %} disabled='disabled' {% else %} style="color:black;" {% endif %} value="{{ single_group.group_id }}">{{ single_group.indent }}{{ single_group.name }} ({{ single_group.abbreviation }})</option>
                  {% endfor %}
                </select>
              </div>
            </div>
          <div id="selected_groups_title">Selected Groups:</div>
          <ul id="selected_groups">

          </ul>
      {% endif %}

      <input type="hidden" name="group_data" id="group_data" />

      <hr style="clear:both;">

      <div class="field-group" style="clear:both;">
        <input class="btn btn-primary" type="submit" value="Save Edits">
      </div>
    </form>
{% endblock %}
{% block js_bottom %}
  {{ parent() }}
  <script type="text/javascript" src="/{{ core_type }}/lib/javascripts/chosen/chosen-0.9.11/chosen.jquery.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/0.3.2/sweet-alert.min.js"></script>

  <script id="single_user_address_template" type="text/template">
    <div id="<%- single_user_address_container_id %>" class="row">
      <!-- Column 1 -->
      <div class="col-sm-6 col-md-6">
        <div class="form-group">
          <label class="control-label" for="label[<%- user_address_id %>]"><span style="color:red;">*</span>Label:</label>
          <div class="controls">
            <input name="label[<%- user_address_id %>]" id="label[<%- user_address_id %>]" type="text" required="true" value="<%- address_label %>" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="address_1[<%- user_address_id %>]"><span style="color:red;">*</span>Address 1:</label>
          <div class="controls">
            <input name="address_1[<%- user_address_id %>]" id="address_1[<%- user_address_id %>]" type="text" required="true" value="<%- address_1 %>" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="address_2[<%- user_address_id %>]">Address 2:</label>
          <div class="controls">
            <input name="address_2[<%- user_address_id %>]" id="address_2[<%- user_address_id %>]" type="text" value="<%- address_2 %>" class="form-control">
          </div>
        </div>
      </div>
      <!-- Column 2 -->
      <div class="col-sm-6 col-md-6">
        <div class="form-group">
          <label class="control-label" for="city[<%- user_address_id %>]"><span style="color:red;">*</span>City:</label>
          <div class="controls">
            <input name="city[<%- user_address_id %>]" id="city[<%- user_address_id %>]" type="text" required="true" value="<%- city %>" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="state[<%- user_address_id %>]"><span style="color:red;">*</span>State:</label>
          <div class="controls">
            <input name="state[<%- user_address_id %>]" id="state[<%- user_address_id %>]" type="text" required="true" value="<%- state %>" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="zip[<%- user_address_id %>]"><span style="color:red;">*</span>Zip Code:</label>
          <div class="controls">
            <input name="zip[<%- user_address_id %>]" id="zip[<%- user_address_id %>]" type="text" required="true" value="<%- zip %>" class="form-control">
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12" style="margin-bottom: 10px;">
        <button type="button" name="remove" class="btn remove_user_address btn-default btn-sm" title="Delete Address" id="delete_user_address_button"><i class="glyphicon glyphicon-remove"></i> Delete Address</button>
      </div>
      <hr>
    </div>
  </script>

  {% if role_perm_modify_own_groups %}

    <script id="single_group_template" type="text/template">
      <li id="<%- single_group_container_id %>" class="single_group_container" data-group="<%- group_id %>">
        <div class="group_description" style="float:left;"><%- group_name %></div>
        <div class="remove_group_container">
          <a href="javascript:void(0);" title="Remove this Group" class="remove-link">remove</a>
        </div>
        <div style="clear:both; margin-top:8px;">
          <div style="clear:both; float:left; margin-right:15px;"><strong>Roles: </strong></div>
          <div style="margin-left:10px; float:left;" class="div_group_select">
            <select multiple="true" size="4" data-placeholder="Select Role(s)..." class="role_select chzn-select">
              <% for (var key in role_choices){ %>
                <option value="<%- role_choices[key]['role_id'] %>" showOnSelect="<%- role_choices[key]['label'] %>"><%- role_choices[key]['label'] %></option>
              <% }; %>
            </select>
          </div>
        </div>
        <div class="proxy_container" style="clear:both;">
        </div>
      </li>
    </script>
    <script id="proxy_template" type="text/template">
    <label>Proxy for: </label>
    <ul class="proxy_list"></ul>
    <input class="user_lookup" placeholder="Start typing to add user..." role="typeahead" type="text" />
    </script>
    <script id="proxy_user_template" type="text/template">
      <li class="proxy_user" data-id="<%- user_account_id %>"><%- displayname %> <a href="javascript:void(0);" role="remove_proxy_user" class="glyphicon glyphicon-remove-circle"></a></li>
    </script>

  {% endif %}

    <script type="text/javascript">
      $(document).ready(function() {

        var address_data = JSON.parse('{{ address_data|json_encode|raw }}');

        {% if role_perm_modify_own_groups %}

          var role_choices = JSON.parse('{{ roles|json_encode|raw }}');
          $("#group").on("change",function(){
            var selected_option = $(this).find(":selected");
            var group_id = selected_option.val();
            var group_name = selected_option.text();
            var exists = $("#selected_groups li.single_group_container[data-group='" + group_id + "']");
            if(group_id != 0 && exists.length == 0){
              add_group(group_id,group_name);
            }
          });

          // Remove group link
          $("#user_account_form").on("click.remove_group", ".remove_group_container",function(){
            $(this).closest("li.single_group_container").remove();
          });

          // Remove proxy user
          $("#user_account_form").on("click.remove_proxy_user","[role='remove_proxy_user']", function(){
              $(this).closest(".proxy_user").fadeOut("fast",function(){
                $(this).remove();
              });
            });

          // Listen for proxys
          $("#user_account_form").on("change.manage_proxy", ".role_select",function(event, existing_proxy_users){
            if($(this).find("option[value='{{ proxy_id }}']:selected").length > 0 && $(this).closest(".single_group_container").find(".proxy_container .proxy_list").length == 0){
              var proxy_template = _.template($("#proxy_template").html());
              var proxy_markup = proxy_template({});
              var proxy_container = $(this).closest("li.single_group_container").find(".proxy_container");
              proxy_container.html(proxy_markup);
              if(typeof existing_proxy_users !== "undefined" && $.isArray(existing_proxy_users)){
                $.each(existing_proxy_users,function(index,single_proxy){
                  add_proxy_user(single_proxy.user_account_id, single_proxy.displayname, proxy_container);
                });
              }
              $(this).closest("li.single_group_container").find("input[role='typeahead']").each(function(){
                $this = $(this);
                $this.typeahead({
                  minLength:2
                  ,items: 10
                  ,source: function(query,process){
                    $.ajax({
                      url:"{{ path_to_this_module }}/find"
                      ,dataType:"json"
                      ,type:"post"
                      ,data: {search:query}
                      ,success:function(data){
                        labels = [];
                        mapped = {};
                        $.each(data, function(i,item) {
                                    mapped[item.displayname] = item.user_account_id;
                                    labels.push(item.displayname);
                                });
                        process(labels);
                      }
                    });
                  }
                  ,updater: function(item){
                    add_proxy_user(mapped[item], item, $this.closest(".proxy_container"));
                          return '';
                  }
                });
              });
            }else{
              //$(this).closest("li.single_group_container").find(".proxy_container").empty();
            }
          });

          function add_proxy_user(user_account_id, displayname, proxy_container){
            var proxy_user_template = _.template(jQuery("#proxy_user_template").html());
            var proxy_user_markup = proxy_user_template({
              user_account_id: user_account_id
              ,displayname: displayname
            });
            proxy_container.find(".proxy_list").append(proxy_user_markup);
          }

          function add_group(group_id, group_name, roles, proxy_users){
            var single_group_container_id = _.uniqueId("single_group_container_");
            var single_group_template = _.template($("#single_group_template").html());
            var single_group_markup = single_group_template({
              group_id: group_id
              ,group_name: group_name
              ,role_choices: role_choices
              ,single_group_container_id: single_group_container_id
            });

            $("#selected_groups").append(single_group_markup);
            if(roles){
              $("#" + single_group_container_id).find("select.role_select").val(roles);
              $("#" + single_group_container_id).find("select.role_select").trigger("change", [proxy_users]);
            }

            $("#selected_groups").animate({scrollTop: $("#selected_groups")[0].scrollHeight},1000);
            $("#" + single_group_container_id).find("select.role_select").chosen();
          }

          // Gather author data
          $("#user_account_form").submit(function(event){
            var group_data = {};
            var counter = 0;
            $("#selected_groups > li").each(function(){
              group_data[counter] = {
                "group_id":$(this).attr("data-group")
                ,"roles": $(this).find("select.role_select").val()
              };
              group_data[counter].proxy_users = $(this).find(".proxy_list .proxy_user").map(function(index,el){
                  return {
                    user_account_id: $(el).data("id")
                    ,displayname: $(el).text()
                  };
                }).get();
              counter++;
            });
            $("#group_data").attr("value",JSON.stringify(group_data));
          });

          // Populate with existing data
          var existing_group_data = JSON.parse('{{ user_account_groups|json_encode|raw }}');
          $.each(existing_group_data,function(key,value){
            add_group(value.group_id,value.group_name,value.roles,value.proxy_users);
          });

        {% endif %}

      function add_user_address(address_label, address_1, address_2, city, state, zip) {
        var address_count = parseInt( $("#address_count").val() );
        var user_address_id = address_count + 1;
        var user_address_container_id = "single_user_address_container_"+user_address_id;
        var single_user_address_template = _.template($("#single_user_address_template").html());
        var user_address_markup = single_user_address_template({
          user_address_id: user_address_id
          ,single_user_address_container_id: user_address_container_id
          ,address_label: address_label
          ,address_1: address_1
          ,address_2: address_2
          ,city: city
          ,state: state
          ,zip: zip
        });
        $("#user_address_div").append(user_address_markup);
        $("#address_count").attr("value", user_address_id);
      }

      // Populate with existing address data
      if(address_data.length > 0) {
        $.each(address_data,function(key, value){
          add_user_address(value.address_label, value.address_1, value.address_2, value.city, value.state, value.zip);
        });
      }

      // Add an address block
      $("#user_account_form").on("click", ".add_user_address", function(event){
        add_user_address();
      });

      // Remove address block
      $("#user_account_form").on("click", ".remove_user_address", function(event){

        var current_address_count = parseInt( $("#address_count").val() );
        $("#address_count").attr( "value", (current_address_count-1) );
        var this_address_block = $(this).parent().prev().parent(".row");

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
          this_address_block.fadeOut(500);
          setTimeout(function() {
            this_address_block.remove();
          }, 1500);
        });

        // if( confirm('Are you sure you want to delete this address?') )
        // {
        //   var current_address_count = parseInt( $("#address_count").val() );
        //   $("#address_count").attr( "value", (current_address_count-1) );
        //   var this_address_block = $(this).parent().prev().parent(".row");
        //   this_address_block.fadeOut(300);
        //   setTimeout(function() {
        //     this_address_block.remove();
        //   }, 1000);
        // }
      });

    });
  </script>
{% endblock %}
