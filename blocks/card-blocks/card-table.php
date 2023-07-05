<?php topten_get_desc(); ?>

<?php $table = get_field( 'table' ); ?>

<?php if ( $table ) : ?>
	<table style="<?php topten_get_block_width(); ?>">
		<?php if ( ! empty( $table['header'] ) ) : ?>
			<tr>
				<?php foreach ( $table['header'] as $cell ) : ?>
					<th>
						<?php echo esc_html( $cell['c'] ); ?>
					</th>
				<?php endforeach; ?>
			</tr>
		<?php endif; ?>

		<?php if ( ! empty( $table['body'] ) ) : ?>
			<?php foreach ( $table['body'] as $row ) : ?>
				<tr>
					<?php foreach ( $row as $cell ) : ?>
						<td>
							<?php echo esc_html( $cell['c'] ); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
<?php endif; ?>
