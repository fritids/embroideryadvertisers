<?php $this->get_template_part('head'); ?>
	<body>
		<div <?php $this->classes('body'); ?>>
			<div <?php $this->classes('wrapper'); ?>>
<?php if (isset($this->args->viewhtml)) { ?>
			<div <?php $this->classes('mail_link'); ?>>
				Email not displaying correctly ? <a href='http://embroideryadvertisers.com'>View it in your browser</a>
			</div>
<?php } ?>
				<table <?php $this->classes('nopmb htable htr'); ?>>	
					<tr>
						<td <?php $this->classes('nopmb txtleft'); ?>>
						<? 
						//$month=date('M');
						$month='Sep';
						?>
							<img src='<? echo $month;?>-maillogo.jpg' alt='img' />
							<? echo $month;?>
						</td>
						<td style='width:50px;'></td>
						<td <?php $this->classes('nopmb'); ?>></td>
					</tr>
				</table>
				<table <?php $this->classes('nopmb htdate'); ?>>
					<tr>
						<td <?php $this->classes('hdate'); ?>>
							<?php echo mysql2date('F j, Y', current_time('mysql')); ?>
						</td>
					</tr>
				</table>
				<div  <?php $this->classes('main'); ?>>
					<div  <?php $this->classes('content'); ?>>
					<div style="float:right; font-size:12px; margin:5px;">All times listed in emails are based on mountain times, unless other wise stated.</div>
					<div style="clear:both;"></div>
<!-- end header -->