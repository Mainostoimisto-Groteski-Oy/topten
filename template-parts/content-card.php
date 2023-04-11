<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

?>

<?php if ( function_exists('yoast_breadcrumb') ) : ?>
	<section class="page-breadcrumbs">
		<div class="grid">
			<?php yoast_breadcrumb( '<p id="breadcrumbs">','</p>' ); ?>
		</div>
	</section>
<?php endif; ?>
<div class="grid">



	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<section class="row-block">
			<div class="grid">
				<div class="column top">
					<?php 
					// Jos post type on kortti, lisätään data
					
						// Kortin ylätunniste
						$id = $post->ID;
						$identifier_start = esc_html(get_field('identifier_start', $id));
						$identifier_end = esc_html(get_field('identifier_end', $id));
						$type = get_post_type($id);
						$version = esc_html(get_field('version', $id));
						$modified = date('j.n.Y', strtotime(esc_html($post->post_modified)));
						$keywords = get_field('card_keywords', $id);
						// Tätä käytetään myöhemmin loopissa mikä alkaa tietenkin nollasta
						$keywords_count = count($keywords)-1;
						?>
						<div class="card-info">
							<div class="date">
								<span class="small">Vahvistuspvm</span>
								<span><?php echo $modified; ?></span>
							</div>
							<div class="spacer">

							</div>
							<?php if('tulkintakortti' === get_post_type() ) : ?>
								<div class="identifier">
									<span class="small">Tunniste</span>
									<div class="wrapper">
										<span class="start"><?php echo $identifier_start; ?></span>
										<span class="end"><sup><?php echo $identifier_end; ?></sup></span>
									</div>
								</div>
							<?php else : ?>
								<div class="identifier dual">
									<div class="start">
										<span class="small"></span>
										<span></span>
									</div>
									<div class="end">
										<span></span>
									</div>
								</div>
							<?php endif; ?>
							<div class="version">
								<span class="small">Muutos</span>
								<span><?php echo $version; ?></span>
							</div>
						</div>
						
				</div>
			</div>
		</section>
		<?php
		// Kortin sisältölohkot
		the_content(); 
		?>
		<section class="row-block">
			<div class="grid">
				<div class="column">
					<div class="card-keywords">
						<span class="desc">Asiasanat</span>
						<?php
						// Kortin asiasanat
						if(!empty($keywords)) : ?>
							<div class="keywords-wrapper">
							<?php foreach ($keywords as $index => $keyword) :
								$term = get_term( $keyword );
								if($index !== $keywords_count) : ?>
									<span class="keyword"><?php echo esc_html($term->name).', '; ?></span>
								<?php else : ?>
									<span class="keyword"><?php echo esc_html($term->name); ?></span>
								<?php endif; 
							endforeach; ?>
							</div>
							<?php
						endif;
						?>
					</div>
				</div>
			</div>
		</section>
	</article>
	<aside class="feedback">
		<span class="h4 red"><strong>Palaute</strong></span>
		<p>
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas placerat porttitor erat pharetra facilisis. Duis rutrum suscipit ex at sodales. Nullam mollis auctor justo sed accumsan. Nam ac metus feugiat, viverra est eget, tempus lectus. 
		</p>

<button class="save-as-pdf" data-type="tulkintakortti">
	Tallenna PDF
</button>

	</aside>
</div>