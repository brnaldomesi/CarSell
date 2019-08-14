<style>
	.woobuilder-lightbox {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background: #fff;
		border: 1px solid #aaa;
		padding: 1em;
		display: none;
		z-index: 99999;
		max-height: 80vh;
		overflow: auto;
	}

	.woobuilder-lightbox:target {
		display: block;
	}

	.woobuilder-lightbox h3 {
		margin-top: 0;
	}

	.woobuilder-lightbox .button {
		vertical-align: middle;
	}

	.woobuilder-lightbox .woobuilder-templates {
		margin: 1em 0;
	}

	.woobuilder-template {
		display: inline-flex;
		margin: 0 .7em 1em 0;
	}

	.woobuilder-template .dashicons {
		line-height: inherit;
		font-size: 14px;
	}

	.woobuilder-template .button.delete-tpl {
		margin-right: 0;
		padding-left: 5px;
		padding-right: 4px;
		border-right: none;
		color: #a00;
		border-radius: 3px 0 0 3px;
	}

	.woobuilder-template .button ~ .button {
		border-left: none;
		border-radius: 0 3px 3px 0;
	}

</style>

<script>
	jQuery( function ( $ ) {
		$( '.delete-tpl' ).click( function ( e ) {
			if ( ! confirm( 'Are you sure you want to delete template ' + $( this ).siblings( '.button' ).text() + '.' ) ) {
				e.preventDefault();
			}
		} );
	} );
</script>

<?php

if ( isset( $_GET['woobuilder-delete-template'] ) ) {
	$delete_tpl = $_GET['woobuilder-delete-template'];
	$custom_tpl = get_option( 'woobuilder_pro_templates', [] );

	if ( $delete_tpl && ! empty( $custom_tpl[ $delete_tpl ] ) ) {
		unset( $custom_tpl[ $delete_tpl ] );
		update_option( 'woobuilder_pro_templates', $custom_tpl, false );
	}
}


$templates = WooBuilder_Blocks::templates( 'reload' );

if ( $templates && 'auto-draft' != get_post_status() ) {
	?>
	<div id="woobuilder-template-picker" class="woobuilder-lightbox">
		<h3>Which template would you like to start with for this product?</h3>
		<p>Using a template would replace product content resulting in irreversible loss of original content.</p>
		<div class="woobuilder-templates">
			<?php

			foreach ( $templates as $tid => $tpl ) {
				$t_uri  = add_query_arg( 'toggle-woobuilder', $tid );
				$t_delete  = add_query_arg( 'woobuilder-delete-template', $tid );
				$t_name = $tpl['title'];
				?>
				<div class="woobuilder-template">
					<?php if ( 0 === strpos( $tid, 'custom' ) ) {
						?>
						<a class='button button-large delete-tpl' href='<?php echo $t_delete ?>#woobuilder-template-picker'>
							<i class="dashicons dashicons-no"></i></a>
						<?php
					} ?>
					<a class='button button-primary button-large' href='<?php echo $t_uri ?>'><?php echo $t_name ?></a>
				</div>
				<?php
			}
			?>
		</div>

		<a href="#" class="button">Continue using default editor.</a>
	</div>
	<?php
}
?>
<div id="woobuilder-enable-dialog" class="woobuilder-lightbox">
	<h3>Would you like to use WooBuilder Blocks for this product?</h3>
	<div class="button-group">
		<a class="button button-primary" href="<?php echo add_query_arg( 'toggle-woobuilder', 1 ); ?>">
			Yeah, use Woobuilder blocks</a>
		<a class="button" href="#">Continue using default editor.</a>
	</div>
</div>
<?php
