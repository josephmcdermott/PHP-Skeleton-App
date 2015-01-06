{% extends layout_template_name %}
{% block styles_head %}
	<style type="text/css">
		.aup{
			width: 800px;
			height: 400px;
			overflow: scroll;
			padding: 12px;
			margin-bottom: 15px;
			border: 1px solid black;
		}
	</style>
{% endblock %}
{% block content %}
	<div class="col-lg-offset-2 col-sm-9 col-md-9">
		<h2>Registration Form</h2>
		{% if errors %}
			<div class="alert alert-danger">
				<h4>Form Errors</h4>
				{% for single_error in errors %}
				<p>{{ single_error }}</p>
				{% endfor %}
			</div>
		{% endif %}
		{% if is_registered is not empty %}
			<div class="alert alert-info">
				<h4>You are already registered.</h4>
				<p>If you would like to update your account, please contact your group administrator.</p>
			</div>
		{% else %}
			<form method="POST" class="form-horizontal">
				{% if needs_group %}
					<label><span style="color:red;">*</span><strong>Select Group:</strong></label>
					{% for single_group in groups %}
						<label class="radio">
							<input {% if single_group.group_id == submitted_data.group %} checked='checked' {% endif %} type="radio" name="group" value="{{ single_group.group_id }}" />
							{{ single_group.indent }}{{ single_group.name }} ({{ single_group.abbreviation }})
						</label>
					{% endfor %}
				{% endif %}

		    <h3>Access Agreement Acceptable Use Policy (AUP)</h3>

		    <div class="aup">
					<p>This Sample Internet Usage Policy applies to all employees of <company> who have access to computers and the Internet to be used in the performance of their work. Use of the Internet by employees of <company> is permitted and encouraged where such use supports the goals and objectives of the business. However, access to the Internet through <company> is a privilege and all employees must adhere to the policies concerning Computer, Email and Internet usage. Violation of these policies could result in disciplinary and/or legal action leading up to and including termination of employment. Employees may also be held personally liable for damages caused by any violations of this policy. All employees are required to acknowledge receipt and confirm that they have understood and agree to abide by the rules hereunder.</p>

					<h4>Computer, email and internet usage</h4>

					<ul>
				    <li>Company employees are expected to use the Internet responsibly and productively. Internet access is limited 		  to job-related activities only and personal use is not permitted</li>
				    <li>Job-related activities include research and educational tasks that may be found via the Internet that would help in an employee's role</li>
				    <li>All Internet data that is composed, transmitted and/or received by &lt;company's&gt; computer systems is 			  considered to belong to &lt;company&gt; and is recognized as part of its official data. It is therefore subject to disclosure for legal reasons or to other appropriate third parties</li>
				    <li>The equipment, services and technology used to access the Internet are the property of &lt;company&gt; and the 		  company reserves the right to monitor Internet traffic and monitor and access data that is composed, sent or received through its online connections</li>
				    <li>Emails sent via the company email system should not contain content that is deemed to be offensive. This 		  includes, though is not restricted to, the use of vulgar or harassing language/images</li>
				    <li>All sites and downloads may be monitored and/or blocked by &lt;company&gt; if they are deemed to be harmful 		  and/or not productive to business</li>
				    <li>The installation of software such as instant messaging technology is strictly prohibited</li>
				  </ul>

					<h4>Unacceptable use of the internet by employees includes, but is not limited to:</h4>
					<ul>
				    <li>Sending or posting discriminatory, harassing, or threatening messages or images on the Internet or via &lt;company's&gt; email service</li>
				    <li>Using computers to perpetrate any form of fraud, and/or software, film or music piracy</li>
				    <li>Stealing, using, or disclosing someone else's password without authorization</li>
				    <li>Downloading, copying or pirating software and electronic files that are copyrighted or without authorization</li>
				    <li>Sharing confidential material, trade secrets, or proprietary information outside of the organizatio</li>
				    <li>Hacking into unauthorized websites</li>
				    <li>Sending or posting information that is defamatory to the company, its products/services, colleagues and/or 		  customers</li>
				    <li>Introducing malicious software onto the company network and/or jeopardizing the security of the 				  organization's electronic communications systems</li>
				    <li>Sending or posting chain letters, solicitations, or advertisements not related to business purposes or activities</li>
				    <li>Passing off personal views as representing those of the organization</li>
					</ul>

					<p>If an employee is unsure about what constituted acceptable Internet usage, then he/she should ask his/her supervisor for further guidance and clarification</p>
					<p>All terms and conditions as stated in this document are applicable to all users of &lt;company's&gt; network and Internet connection. All terms and conditions as stated in this document reflect an agreement of all parties and should be governed and interpreted in accordance with the policies and procedures mentioned above. Any user violating these policies is subject to disciplinary actions deemed appropriate by &lt;company&gt;.</p>

				</div>

				<h4>User compliance</h4>
				
				<label class="checkbox">
					<input type="checkbox" name="acceptable_use_policy" value="1">
					<span style="color:red;">*</span>I understand and will abide by this Sample Internet Usage Policy. I further understand that should I commit any violation of this policy, my access privileges may be revoked, disciplinary action and/or appropriate legal action may be taken.
				</label>

	    	<div class="control-group">
    			<input class="btn btn-primary" type="submit" value="Submit" />
    		</div>
			</form>
		{% endif %}
	</div>
{% endblock %}
{% block js_bottom %}
	{{ parent() }}
	<script type="text/javascript">
    	$(document).ready(function(){
    	});
    </script>
{% endblock %}
