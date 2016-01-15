<div class="promo-header text-center">
	<h1><?php echo $promo->name; ?></h1>
	<?php if (!empty($promo->image)) { ?>
	<div class="promo-header-container">
		<img class="promo-img" src="<?php echo $promo->image; ?>">
	</div>
	<?php } ?>

	<hr class="promo-separator">
</div>

<div class="row">
	<div class="col-md-11">
		<?php if (!empty($form_error)) { ?>
		<div class="form-error"><?php echo $form_error; ?></div>
		<?php } ?>
		<form role="form" name="form_register" action="<?php echo $uzn_promos_url_register; ?>" method="post">
			<div class="form-group">
				<label for="name">Your Name:</label>
				<input class="form-control" type="text" name="name" value="<?php echo esc_attr($form['name']); ?>">
			</div>
			<div class="form-group">
				<label for="email">Email:</label>
				<input class="form-control" type="text" name="email" value="<?php echo esc_attr($form['email']); ?>">
			</div>
			<p class="form-terms">By proceeding I agree to the terms of service</p>
			<input class="btn btn-default" id="btn-register" type="submit" value="Enter Giveaway">
		</form>

	</div>
	<div class="col-md-2">
	</div>
	<div class="col-md-11">
		<p class="promo-description">
			<?php echo $promo->description; ?>
		</p>
	</div>
</div>