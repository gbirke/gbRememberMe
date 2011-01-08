<?php use_helper('I18N') ?>
<h2><?php echo __('Remembered logins overview', null, 'gb_remember_me');?></h2>
<p><?php echo format_number_choice('[0]You not remembered on any computer.|[1]You are remembered on one computer.|[1,+Inf]You are remembered on %count% computers.',
 array('%count%' => '<em>'.$logins.'</em>'), $logins, 'gb_remember_me'); ?></p>
<?php if($logins > 0): ?>
<p><?php echo link_to(__('Remove all remembered computers', null, 'gb_remember_me'), 'gbSecurity/removetokens'); ?>
<?php endif; ?>