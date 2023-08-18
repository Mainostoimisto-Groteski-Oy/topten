<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Groteski
 */

?>
<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
	<section class="page-breadcrumbs">
		<div class="grid">
			<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
		</div>
	</section>
<?php endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	$image = get_the_post_thumbnail_url( $post->ID, 'fullhd' );
		// get the alt if image exists
	if ( $image ) {
		$image_id = attachment_url_to_postid( $image );
		$alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	} else {
		$alt = '';
	}
	?>
	<div class="grid">
		<?php if ( $image ) : ?>
			<div class="article-image">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
			</div>
		<?php endif; ?>

		<div class="entry-meta">
			<?php
			$id = get_the_ID();
			// get the post date by post id
			$date = get_the_date( 'j.n.Y', $id );
			?>

			<p class="date">
			<?php
			if ( ! empty( $date ) ) {
				echo esc_html( $date ); }
			?>
			</p>

		</div><!-- .entry-meta -->

		<?php the_title( '<h1 class="article-title h2">', '</h1>' ); ?>
		<div class="article-text">
			<?php the_content(); ?>
		</div>

	</div>
</article>




<?php
if ( get_field( 'article_author' ) || get_field( 'single_article_social', 'options' ) ) :

	if ( get_field( 'article_author' ) && get_field( 'single_article_social', 'options' ) ) {
		$section_class = 'both';
	} else {
		$section_class = 'one';
	}
	?>

	<section class="author-and-sharing <?php echo esc_attr( $section_class ); ?>">
		<div class="grid">
		<?php
		if ( get_field( 'article_author' ) ) :
			$object = get_field( 'article_author' );
			$id     = $object[0]->ID;

			$name  = get_field( 'name', $id );
			$title = get_field( 'title', $id );
			$phone = get_field( 'phone', $id );
			$email = get_field( 'email', $id );
			if ( get_field( 'image', $id ) ) {
				$image       = get_field( 'image', $id );
				$image_url   = $image['sizes']['medium'];
				$image_alt   = $image['alt'];
				$image_class = '';
			} else {
				$image_url   = '';
				$image_class = 'placeholder';
			}
			?>

			<div class="author-info">
				<div class="image <?php echo esc_attr( $image_class ); ?>" >
				<?php if ( ! empty( $image_url ) ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
				<?php else : ?>
					<div class="image-placeholder"></div>
				<?php endif; ?>
				</div>
				<h2 class="title h4"><?php echo esc_html( $name ); ?></h2>
				<span><?php echo esc_html( $title ); ?></span>
				<span><?php echo esc_html( $phone ); ?></span>
				<a class="email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></span></a>
			</div>
		<?php endif; ?>
		<?php if ( get_field( 'single_article_social', 'options' ) ) : ?>
			<div class="social">
				<h3 class="title h4"><?php the_field( 'single_article_social_title', 'options' ); ?></h3>
				<div class="socials">
					<?php
					// Social media sharing buttons for Facebook, Twitter and LinkedIn
					// get the current post url
					$url = get_permalink();

					if ( get_field( 'single_article_fb', 'options' ) ) :
						?>
					<div class="social-sharing">
						<a href="https://www.facebook.com/share.php?u=<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="facebook">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/dist/images/somejakokuvat_facebook.png' ); ?>" />
							<span class="screen-reader-text"><?php esc_html_e( 'Jaa Facebookissa', 'topten' ); ?></span>
						</a>
					</div>
					<?php endif; ?>

					<?php if ( get_field( 'single_article_twitter', 'options' ) ) : ?>
					<div class="social-sharing">
						<a class="twitter-share-button"
							href="https://x.com/intent/tweet?url=<?php echo esc_url( $url ); ?>"
						data-size="large">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/dist/images/x_x.png' ); ?>" />
						<span class="screen-reader-text"><?php esc_html_e( 'Jaa X:ssä', 'topten' ); ?></span>
						</a>
					</div>
					<?php endif; ?>

					<?php if ( get_field( 'single_article_linkedin', 'options' ) ) : ?>
					<div class="social-sharing">
						<?php // linkedin sharing button ?>
						<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_url( $url ); ?>">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/dist/images/somejakokuvat_linkedin.png' ); ?>" />
							<span class="screen-reader-text"><?php esc_html_e( 'Jaa LinkedInissä', 'topten' ); ?></span>
						</a>

					</div>
				</div>

				<?php endif; ?>


				<div class="text"><?php the_field( 'single_article_social_text', 'options' ); ?></div>
				<?php if ( get_field( 'single_article_social_button', 'options' ) ) : ?>
					<?php
					$link        = get_field( 'single_article_social_button', 'options' );
					$link_url    = $link['url'];
					$link_title  = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>

				<div class="buttons">
					<a target="<?php echo esc_url( $link_target ); ?>" href="<?php esc_url( $link_url ); ?>" class="button <?php echo esc_html( get_field( 'single_article_social_button_icon', 'options' ) ); ?>"><?php echo esc_html( $link_title ); ?></a>
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		</div>
	</section>

<?php endif; ?>

<?php
// get 3 latest posts but exclude current post from the list
$args      = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post__not_in'   => array( $post->ID ),
);
$postslist = get_posts( $args );
if ( $postslist ) :
	?>

	<section class="latest-articles">
		<div class="content">
			<div class="grid post-wrapper">
				<?php
				foreach ( $postslist as $post ) :
					get_template_part( 'template-parts/content-after-single' );
					endforeach;
				?>
			</div>
		</div>
	</section>

<?php endif; ?>

