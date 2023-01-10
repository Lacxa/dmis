<?php 
$this->load->view('pages/e_reports/header.php');


$employee_name = empty($basic['emp_mname']) ? @$basic['emp_fname'].' '.@$basic['emp_lname']: @$basic['emp_fname'].' '.@$basic['emp_mname'][0].'. '.@$basic['emp_lname'];

echo '<p style="text-align: center; text-transform: uppercase; font-weight: bolder;"><span style="font-size: 14px;">NIT DISPENSARY LABORATORY REPORT, DATED FROM ' . $start . ' TO ' . $end . '</span> <br /><span style="font-size: 12px;">(' . @$employee_name . ' - ' .@$basic['emp_pf']. ')</span></p>';
?>


<?php if(!empty(@$data)) { ?>
	<br />
	<table style="width: 100%;font-size: 11px; table-layout: auto; border: 1px solid black; border-collapse: collapse;">	
		<tr nobr="true" style="font-weight: bolder;"> 
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 4%;"> # 
			</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 16%">PATIENT
			</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 4%;">AGE
			</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 11%;">ADDRESS
			</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 10%;">OCCUPATION</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 7%">DATE
			</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 48%">DIOGNOSTICS
			</th>
		</tr>
		<?php foreach ($data as $key => $row) { ?>
			<tr nobr="true">
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 4%;"><?php echo @$key+1;?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 16%;"><?php echo @$row['lname'].',&nbsp;'.@$row['fname'].' ('.@$row['gender'][0].')'.'<br>('.@$row['pf'].')'; ?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 4%;"><?php echo @$row['age'];?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 11%;"><?php echo @$row['address'];?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 10%;"> <?php echo @$row['occupation'];?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 7%"><?php echo @$row['day']; ?>					
				</td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 48%;">
					<?php
					if(!empty($row['diagnosis'])) {
						$numItems = count($row['diagnosis']);
						$i = 0;
					foreach($row['diagnosis'] as $key=>$value) {
						echo '<em>'.$value['parent'].'</em> | '.$value['name'].': '.$value['results'];
						echo (++$i === $numItems) ? '' : ' --- ';
					}}else{echo 'None';}
					 ?>					
				</td>
				</tr><?php } ?>

				<tr nobr="true">
					<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; text-align: right;font-weight: bolder;" colspan="6"> Total:
					</td>
					<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-weight: bolder;"> <?php echo count(@$data);?></td>
				</tr>
			</table>
		<?php } ?>
