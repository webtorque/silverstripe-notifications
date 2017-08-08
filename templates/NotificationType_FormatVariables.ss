You can use the following variables for this notification:
<ul>
	<li><code>{\$Member.FirstName}</code></li>
	<li><code>{\$Member.Surname}</code></li>
	<li><code>{\$Member.Email}</code></li>
	<li><code>{\$CallToActionURL}</code></li>
	<% loop $FormatVariablesList %>
	<li><code>{\${$Me.Text}}</code></li>
	<% end_loop %>
</ul>
