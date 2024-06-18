<?php

/**
 * Template Name: Booking Page
 *
 */
 
get_header();
?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 class="main-booking-title">Στοιχεία Κράτησης</h1>
			</div>
		</div>
		<div class="booking">
			<div class="row">

				<?php
					if (!isset($_GET['id'])) {
					//	die();
					}
					
					$product = wc_get_product( $_GET['id'] );

					$valuesHotel = wc_get_product_terms( $product->id, 'pa_hotel', array( 'fields' => 'all' ) );
					/*foreach($valuesHotel as $val) {
						var_dump($val);
					}*/
				?>
				<input type="hidden" id="productid" value="<?php echo $_GET['id']; ?>">

				<div class="col-md-3">
					<div class="form-group">
					    <label for="hotel">Ξενοδοχείο</label>
					    <select id="hotel">
					    	<option value="0">Επιλέξτε</option>
							<?php foreach ($valuesHotel as $value) : ?>
								<option value="<?php echo $value->slug ?>"><?php echo $value->name; ?></option>
							<?php endforeach; ?>
							
						</select>
					  </div>
				</div>
				<div class="col-md-3 adults">
					<div class="form-group">
					    <label for="adults">Ενήλικες</label>
					    <input type="number" class="form-control" id="adults" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children313">
					<div class="form-group">
					    <label for="children">Παιδιά 3-13</label>
					    <input type="number" class="form-control" id="children313" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children512">
					<div class="form-group">
					    <label for="children">Παιδιά 5-12</label>
					    <input type="number" class="form-control" id="children512" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children213">
					<div class="form-group">
					    <label for="children">Παιδιά 2-13</label>
					    <input type="number" class="form-control" id="children213" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children212">
					<div class="form-group">
					    <label for="children">Παιδιά 2-12</label>
					    <input type="number" class="form-control" id="children212" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children12150">
					<div class="form-group">
					    <label for="children">Παιδιά > 12</label>
					    <input type="number" class="form-control" id="children12150" placeholder="0">
					  </div>
				</div>
				<div class="col-md-3 children children13150">
					<div class="form-group">
					    <label for="children">Παιδιά > 13</label>
					    <input type="number" class="form-control" id="children13150" placeholder="0">
					  </div>
				</div>
				<div class="col-md-12">
					<button type="button" class="btn btn-dark continue">Συνέχεια</button>
				</div>

				<div class="col-md-3"></div>
				<div class="col-md-3 secondstep">
					<div class="form-group">
					    <label for="roomtype">Τύπος Δωματίου</label>
					    <select id="roomtype">
						</select>
					  </div>
				</div>
				<div class="col-md-3 secondstep text-center">
					<div class="form-group">
					    <label for="price">Τιμή</label>
					    <div class="price"></div>
					</div>
				</div>
				<div class="col-md-6 secondstepempty text-center">
					<div class="form-group">
					    <label>Sorry there are no available options.</label>
					</div>
				</div>

				<div class="col-md-12 text-center">
					<div class="btn btn-dark submitorder" data-quan="1" data-product_id="<?php echo $_GET['id']; ?>" data-variation_id="" data-product_sku="<?php echo $product->get_sku(); ?>" tabindex="0">
						Κάνε κράτηση
					</div>
					
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>