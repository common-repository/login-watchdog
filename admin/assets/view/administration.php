<?php 
//blocking direct access to plugin 
defined('ABSPATH') or die();
?>
<div class="wrap">
	<h2>Login Watchdog</h2> 
					  
	<?php if(isset($_GET['tab'])): ?>
		<?php $active = $_GET['tab']; ?>
	<?php else:?>
		<?php $active = 'logs';?>
	<?php endif;?>
 
	<h2 class="nav-tab-wrapper">
		<a href="?page=login-watchdog&tab=logs" class="nav-tab <?= $active == 'logs' ? 'nav-tab-active' : ''?>">
			<?= __("Logs","login-watchdog");?>
		</a>
		<?php if(current_user_can('update_core')):?>
			<a href="?page=login-watchdog&tab=settings" class="nav-tab <?= $active == 'settings' ? 'nav-tab-active' : ''?> dashicons-before dashicons-admin-settings">
				<?= __("Settings","login-watchdog");?>
			</a>		
		<?php endif;?>		
	</h2>
			
	<?php if($active == "settings"): ?>
		<?php if(current_user_can('update_core')):?>
			<form method="post" action="options.php">
				<?php settings_fields('login-watchdog');?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?=__("The number of failed attempts", "login-watchdog")?></th>
						<td>
							<input type="number" 
								   name="WATCHDOG_LOGIN_ATTEMPS"
								   min="1" max="10" 
								   value="<?= esc_attr(get_option('WATCHDOG_LOGIN_ATTEMPS', 3))?>">
						   <p class="description">
							   <?=__("The number of failed login attempts, after which the IP address will be blocked.", "login-watchdog")?>
						   </p>
						</td>
					</tr>					 
					<tr valign="top">
						<th scope="row"><?=__("Lockout lenght (in minutes)", "login-watchdog")?></th>
						<td>
							<input type="number" 
								   name="WATCHDOG_LOGIN_TIME_LOCKDOWN"
								   min="10" max="<?= 60*24*30 ?>" step="10"
								   value="<?= esc_attr(get_option('WATCHDOG_LOGIN_TIME_LOCKDOWN', 30))?>">
						   <p class="description">
							   <?=__("How long will be the address blocked (in minutes).", "login-watchdog")?>
						   </p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?=__("Maximum number of displayed records", "login-watchdog")?></th>
						<td>
							<input type="number" 
								   name="WATCHDOG_RECORDS_LIMIT"
								   min="1" step="1" max="10000"
								   value="<?= esc_attr(get_option('WATCHDOG_RECORDS_LIMIT', 30))?>">
						   <p class="description">
							   <?=__("Maximum number of displayed records in logs table.", "login-watchdog")?>
						   </p>
						</td>
					</tr>	
				</table>
				<?php submit_button(); ?>	
			</form>
			<form method="post">
				<hr>
				<h2 class="title"><?= __("Records", "login-watchdog")?></h2>
				<p class="description">
					<?=__("Do you want to delete the database with records of failed login?", "login-watchdog")?> <strong><?= $this->recordsCount ?></strong> <?=__("rec. store in database.", "login-watchdog")?> 
				</p>
				<?php submit_button(__("Delete all records", "login-watchdog"), '', 'deleteAllRecords', false) ?>		
			</form>	
		<?php endif;?>	
	<?php else: ?>	
		<br>
		<small style="float: right;"><?= __("Showing", "login-watchdog") ?> <?= $this->displayedRecordsCount ?> <?= __(" out of", "login-watchdog") ?> <?= $this->recordsCount ?></small>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="manage-column check-column num">
                                            <a href="?page=login-watchdog&tab=logs">
                                                <img src="<?=plugins_url( '../img/refresh.png', __FILE__ )?>" 
                                                     alt="refresh" 
                                                     title="<?= __("Refresh","login-watchdog");?>"
                                                     style="cursor: pointer;">
                                            </a>
					</td>
					<th scope="col" class="manage-column column-primary ">
						<span><?=__("IP", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column">
						<span><?=__("OS", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column">
						<span><?=__("Username", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column">
						<span><?=__("Date", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column">
						<span><?=__("Country", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column">
						<span><?=__("Region", "login-watchdog")?></span>
					</th>
					<th scope="col" class="manage-column num">
						<span><?=__("No. of failures", "login-watchdog")?></span>
					</th>	
				</tr>
			</thead>
			<tbody id="the-list">
				<?php if(empty($this->records)):?>
					<tr>
						<td colspan="8"><b><?=__("There is no record", "login-watchdog")?></b></td>
					</tr>
				<?php else: ?>
					<?php foreach($this->records as $record): ?>				
					<tr>
						<th scope="row" class="check-column"></th>
						<td class="column-primary has-row-actions">
							<?php $timeAttempt = $record->time_attempt; ?>
							<?php $timeAttempt += $this->watchdogLoginTimeLockdown*60; ?>
							<?php if($timeAttempt >= time() && $record->counter >= $this->watchdogLoginAttemps):?>
								<strong style="color: #e14d43;" title='<?=__("Currently blocked", "login-watchdog")?>'>
									<?= $record->ip_address ?> (<?=__("blocked", "login-watchdog")?>)								
								</strong>
							<?php else: ?>
								<strong>
									<?= $record->ip_address ?>
								</strong>
							<?php endif; ?>		
							<div class="row-actions">
								<span class="edit">
									<a href="?page=login-watchdog&amp;ip=<?= $record->ip_address ?>&amp;action=trash" class="submitdelete" ><?=__("Remove", "login-watchdog")?></a>
								</span>
							</div> 
						</td>
						<td> 
							<img src="<?=plugins_url( '../img/'.($record->os != null ? $record->os : 'Unknown').'.png', __FILE__)?>" alt="OS: <?= $record->os ?>" title="OS: <?= $record->os ?> | Info: <?= $record->user_agent_info ?>">							
						</td>
						<td> 
							<?= $record->username ?>
						</td>
						<td>
							<?= date("Y-m-d H:i:s T",$record->time_attempt)?>
						</td>
						<td class="country-name">
							<?php if($record->country_name != null):?>
								<a href="//www.openstreetmap.org?mlat=<?=$record->latitude?>&mlon=<?=$record->longitude?>#map=12/<?=$record->latitude?>/<?=$record->longitude?>" 
								   target="_blank"
								   title='<?=__("Show on map", "login-watchdog")?>'>
									<?= $record->country_name ?>
								</a>
							<?php else:?>
								<img class="iploader" src="<?=plugins_url( '../img/loader.gif', __FILE__ )?>" alt="loading" title="<?= __("Loading...","login-watchdog");?>" data-ip="<?=$record->ip_address?>">
							<?php endif;?>
						</td>
						<td class="region-name">
							<?php if($record->region_name != null):?>
								<a href="//www.openstreetmap.org?mlat=<?=$record->latitude?>&mlon=<?=$record->longitude?>#map=12/<?=$record->latitude?>/<?=$record->longitude?>" 
								   target="_blank"
								   title='<?=__("Show on map", "login-watchdog")?>'>
									<?= $record->region_name ?>
								</a>
							<?php endif;?>
						</td>
						<td class="num">
							<span><?= $record->counter ?></span>
						</td>
					</tr>
					<?php endforeach; ?>
				<?php endif;?>
			</tbody>
		</table>	
		<br>
		<?=__("Made with <span style='color: #e14d43;'>&hearts;</span> by JkmAS", "login-watchdog")?>
	<?php endif; ?>  
</div>    
