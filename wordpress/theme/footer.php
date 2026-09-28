</main>
<footer class="foot">
	<div class="wrap">
		<div class="foot-grid">
			<div class="foot-brand">
				<?php echo tr_brand(); // phpcs:ignore ?>
				<p><?php echo esc_html( tr( 'Traduzioni, interpretariato e mediazione linguistico-culturale tra arabo e italiano. Lezioni di italiano per arabofoni.' ) ); ?></p>
				<?php tr_lang_switch(); ?>
			</div>
			<div><p class="foot-h"><?php echo esc_html( tr( 'Servizi' ) ); ?></p><?php tr_nav_list( 'footer' ); ?></div>
			<div><p class="foot-h"><?php echo esc_html( tr( 'Lo studio' ) ); ?></p><?php tr_nav_list( 'legal' ); ?></div>
			<div>
				<p class="foot-h"><?php echo esc_html( tr( 'Contatti' ) ); ?></p>
				<ul class="foot-c">
					<li><?php echo tr_icon( 'wa' ); ?><a href="<?php echo esc_url( tr_wa_url() ); ?>" target="_blank" rel="noopener"><?php echo tr_ltr( tr_opt( 'tr_phone' ) ); ?></a></li>
					<li><?php echo tr_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( tr_opt( 'tr_email' ) ); ?>"><?php echo tr_ltr( tr_opt( 'tr_email' ) ); ?></a></li>
					<li><?php echo tr_icon( 'pin' ); ?><span><?php echo esc_html( tr( 'Studio a [Città] · online in tutta Italia' ) ); ?></span></li>
					<li><?php echo tr_icon( 'clock' ); ?><span><?php echo esc_html( tr( 'Lun–Ven · 9:00–18:00' ) ); ?></span></li>
				</ul>
			</div>
		</div>
		<div class="foot-bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php echo esc_html( tr( 'P.IVA' ) ); ?> <?php echo tr_ltr( tr_opt( 'tr_piva' ) ); ?></span>
			<span class="based"><span class="flag-it" aria-hidden="true"></span><?php echo esc_html( tr( 'Con sede in Italia' ) ); ?></span>
		</div>
	</div>
</footer>
<a class="fab" href="<?php echo esc_url( tr_wa_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( tr( 'Scrivimi su WhatsApp' ) ); ?>"><?php echo tr_icon( 'wa' ); ?><span><?php echo esc_html( tr( 'WhatsApp' ) ); ?></span></a>
<div class="mbar">
	<a class="btn btn-wa" href="<?php echo esc_url( tr_wa_url() ); ?>" target="_blank" rel="noopener"><?php echo tr_icon( 'wa' ); ?><?php echo esc_html( tr( 'WhatsApp' ) ); ?></a>
	<a class="btn btn-primary" href="<?php echo esc_url( tr_contact_url() ); ?>"><?php echo esc_html( tr( 'Richiedi preventivo' ) ); ?></a>
</div>
<?php wp_footer(); ?>
</body>
</html>
