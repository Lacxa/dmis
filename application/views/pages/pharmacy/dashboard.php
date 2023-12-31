<?php $this->load->view('templates/base_header.php'); ?>

<section class="section dashboard">
	<div class="row">

		<div class="col-12">
			<div class="card recent-sales overflow-auto">
				<div class="card-body">
					<h5 class="card-title">
						Stock Status <span>| All posted batches</span>
					</h5>
          <table class="table table-borderless table-sm nowrap" id="table_stock_status">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Stock</th>
                <th scope="col">Category</th>
                <th scope="col">Form</th>
                <th scope="col">Unit</th>
                <!-- <th scope="col">Intial</th> -->
                <th scope="col">Available</th>
                <th scope="col">Status</th>
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
		const messageTop = `Stock status`;
		var table_stock_status = $('#table_stock_status').DataTable({
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
			url : "<?php echo base_url('pharmacy/stock-status');?>",
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
