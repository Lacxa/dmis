<?php $this->load->view('pages/e_reports/header.php'); ?>
<h4 style="text-decoration: underline;text-decoration-color: black;">PATIENT REPORT</h4>

<table style="width: 100%;font-size: 14px;">
	<tr>
		<td>
			<table style="width: 100%;font-size: 14px;">
				<tr><td>TO:</td></tr>
				<tr><td style=""><?php if(empty($basic['pat_mname'])) echo @$basic['pat_fname'].'&nbsp;'.@$basic['pat_lname'].','; else echo @$basic['pat_fname'].'&nbsp;'.@$basic['pat_mname'].'&nbsp;'.@$basic['pat_lname'].','; ?></td></tr>
				<tr>
					<td style="text-transform: uppercase;"><?php echo @$basic['pat_address'];?>.</td>
				</tr>
				<tr><td><b>PF</b>: <?php echo @$basic['pat_file_no'];?></td></tr>
				<tr><td><b>Mobile</b>: <?php echo @$basic['pat_phone'];?></td></tr>
			</table>
		</td>
		<td>
			<table style="width: 100%;font-size: 14px;">
				<tr>
					<td style="text-align: right;"> <b>Office Name: </b>NIT DISPENSARY
					</td>
				</tr>
				<tr>
					<td style="text-align: right;font-size: 12px;">
						<b>Print: </b><?php echo date('F j, Y, g:i a');?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php foreach ($data as $row) { ?>
	<br />
	<table style="width: 100%;font-size: 10px;" id="t01">
		<tr>
			<td colspan="5" style="text-align:center;"> <strong> THE SERVICE AS PER <?php echo date('F j, Y, g:i a', strtotime($row['rec_regdate']));?> </strong><br>
			</td>
		</tr>
		<tr>
			<td colspan="5"> <strong> PRELIMINARY TEST RESULTS </strong><br>
			</td>
		</tr>
		<tr>
			<td><strong>Blood Pressure</strong></td>
			<td><strong>Pulse Rate</strong></td>
			<td><strong>Weight</strong> </td>
			<td><strong>Height </strong> </td>
			<td><strong> Temperature </strong> </td>
		</tr>
		<tr>
			<td><?php echo @$row['rec_blood_pressure'].'&nbsp;mmHg';?></td>
			<td><?php echo @$row['rec_pulse_rate'].'&nbsp;bit/min';?></td>
			<td><?php echo @$row['rec_weight'].'&nbsp;kg';?></td>
			<td><?php echo @$row['rec_height'].'&nbsp;cm';?></td>
			<td><?php echo @$row['rec_temeperature'].'&deg;C';?></td>
		</tr>
		<tr>
			<td colspan="5" style="background: #ccc;"> <br> </td>
		</tr>
		<tr>
			<td colspan="2"> <strong> SIGNS & SYMPTOMS </strong><br>
			</td>
			<td colspan="3"> <strong> LAB INVESTIGATIONS </strong><br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php echo @$row['sy_descriptions'];?>
			</td>
			<td colspan="3">
				<?php if(isset($row['inv'])){?>
					<ul>
						<?php foreach ($row['inv'] as $key => $value) {
							echo '<li><b>'.@$value['name'].'</b>:&nbsp;'.@$value['result'];
						}?>						
					</ul>
				<?php } else echo '<p>NILL</p>'; ?>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="background: #ccc;"> <br> </td>
		</tr>
		<tr>
			<td colspan="2"> <strong> IDENTIFIED DISEASES </strong><br>
			</td>
			<td colspan="3"> <strong> MEDICATIONS </strong><br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php if(isset($row['diseases'])){?>
					<ul>
						<?php foreach ($row['diseases'] as $key => $value) {
							echo '<li><b>'.@$value['text'].'('.@$value['short'].')</b>';
						}?>						
					</ul>
				<?php } ?>			</td>
			<td colspan="3">
				<?php if(isset($row['medicines'])){?>
					<ul>
						<?php foreach ($row['medicines'] as $key => $value) {
							if(@$value['text'] == 'NILL') echo '<li><b>'.@$value['doctor_desc'].'(*)'; else echo '<li><b>'.@$value['text'].'('.@$value['short'].')</b>:&nbsp;'.@$value['doctor_desc'];
						}?>						
					</ul>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">Medical Officer:&nbsp;<?php echo @$row['doctor'];?></td>
			<td colspan="2">Signature: </td>
		</tr>
	</table>
	<br />
	<br />
<?php } ?>


	<!-- <h1>Codeigniter 3 - Generate PDF from view using dompdf library with example</h1>
	<table style="border:1px solid red;width:100%;">
		<tr>
			<th style="border:1px solid red">Id</th>
			<th style="border:1px solid red">Name</th>
			<th style="border:1px solid red">Email</th>
		</tr>
		<tr>
			<td style="border:1px solid red">1</td>
			<td style="border:1px solid red">Hardik</td>
			<td style="border:1px solid red">hardik@gmail.com</td>
		</tr>
		<tr>
			<td style="border:1px solid red">2</td>
			<td style="border:1px solid red">Paresh</td>
			<td style="border:1px solid red">paresh@gmail.com</td>
		</tr>
	</table> -->

	<?php $this->load->view('pages/e_reports/footer.php'); ?>