<?php $this->load->view('templates/base_header.php'); ?>

<section class="section dashboard">
	<div class="row">

		<div class="col-12">
			<div class="card recent-sales overflow-auto">
				<div class="card-body">
					<h5 class="card-title">
						Sign In History <span>| Recent 50</span>
					</h5>
          <table class="table table-borderless table-sm nowrap" id="table_login_history">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">IP Address</th>
                <th scope="col">Platform</th>
                <th scope="col">Browser</th>
                <th scope="col">Time</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
			</div>
		</div>
	</div>

</div>
</section>

<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
	$(function() {
		const title = '<?php echo $title; ?>';
		const messageTop = `Sign In History`;
		var table_login_history = $('#table_login_history').DataTable({
			oLanguage: {
			sProcessing: "loading...",
			sLengthMenu: 'Show <select class="form-select">'+
			'<option value="10">10</option>'+
			'<option value="50">50</option>'+
			'<option value="100">100</option>'+
			'<option value="500">500</option>'+
			'<option value="-1">All</option>'+
			'</select> records'
		},
		responsive: true,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax": {
			url : "<?php echo base_url('home/sign-in-history/'.@$header);?>",
     		 type : 'POST'
			},
			"ordering": false,
			"dom": 'Blfrtip',
			"buttons": [
				{extend: "copy",title: title,messageTop: messageTop,},
				{extend: "excel",title: title,messageTop: messageTop,},
				{extend: "csv",title: title,messageTop: messageTop,},
				{extend: "pdf",title: title,messageTop: messageTop,},
				{extend: "print",title: title,messageTop: messageTop,},
			],
		});


	});
</script>
