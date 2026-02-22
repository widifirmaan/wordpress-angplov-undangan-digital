<?php

/**
 * Main renderer for the Review Banner
 *
 * @category Child Plugin
 * @author iClyde <kontakt@iclyde.pl>
 */

// Namespace
namespace Inisev\Subs;

// Disallow direct access
if (!defined('ABSPATH'))
  exit;

$backupblissPricing = 'https://backupbliss.com/pricing';
$bbStorage = 'https://storage.backupbliss.com';
$bmiPremium = 'https://backupbliss.com';

?>

<div class="bmi-banner" id="new-bb-banner">
  <!-- Close (X) button -->
  <img src="<?php echo $this->_asset('imgs/bg.svg'); ?>" alt="Left background" class="bmi-banner__left-bg" />
  <a href="#" class="bmi-banner__close" target="_blank">×</a>

  <div class="bmi-banner__header">
    <div>
  You seem to like the <b>Duplicate Post</b> plugin. Then you’ll love <span class="bb-highlight">Backup
        Migration</span>
    </div>
  </div>
  <div class="bmi-banner__subheader">
    We completely re-launched the plugin, and it’s now <b>the best in the market</b> (and <a href="<?php echo $backupblissPricing; ?>" target="_blank" class="bmi-links">most affordable</a>):
  </div>

  <div class="bmi-banner__cards">
    <!-- Card 0 -->
    <div class="bmi-banner__card" id="bmi-banner__card-fast-backup" style="display: none;">
      <div class="bmi-banner__card-header">
        <img src="<?php echo $this->_asset('imgs/fast-backups.svg'); ?>" alt="Fast backup icon" />
        <span>Super-fast backups</span>
      </div>
      <span class="bmi-banner__card-text">
        Backups are quick, reliable, and migrations only takes a few clicks.
      </span>
    </div>



    <!-- Card 1 -->
    <div class="bmi-banner__card" id="bmi-banner__card-free-external-storage" style="display: none;">
      <div class="bmi-banner__card-header">
        <img src="<?php echo $this->_asset('imgs/cloud-options.svg'); ?>" alt="Cloud storage icons" />
        <span>
          Many <div class="bmi-banner__free-underlined"><span>free</span></div> external storage options
        </span>
      </div>
      <span class="bmi-banner__card-text">
        You can now save your backups automatically on
        <b>Google Drive, Dropbox, Amazon S3, FTP</b> etc. for free.
      </span>
    </div>

    <div class="bmi-banner__card bmi-banner__premium-card" id="bmi-banner__card-premium-external-storage"
      style="display: none;">
      <div class="bmi-banner__img-wrapper">
        <img src="<?php echo $this->_asset('imgs/premium-cloud-options.svg'); ?>" alt="Cloud storage icons" />
      </div>
      <div class="bmi-banner__card-content">
        <div class="bmi-banner__card-header">
          <span>
            More external storage options
          </span>
        </div>
        <div class="bmi-banner__card-text">
          <span>
            You can now save your backups automatically on
            <b>Dropbox, Amazon S3, FTP</b> etc. for free.
          </span>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="bmi-banner__card" id="bmi-banner__card-free-storage" style="display: none;">
      <div class="bmi-banner__card-header">
        <img src="<?php echo $this->_asset('imgs/1gb-free.svg'); ?>" alt="1 GB free icon" />
        <span>1 GB of <br>
          <div class="bmi-banner__free-underlined"><span>free</span></div> storage
        </span>
      </div>
      <span class="bmi-banner__card-text">
        We added our <b>own storage option,</b> giving you
        1 GB of free space (and
        <a href="<?php echo $backupblissPricing; ?>" target="_blank" class="bmi-links">very affordable</a>
        plans for more)! <a href="<?php echo $bbStorage; ?>" target="_blank" class="bmi-links">Learn more</a>
      </span>
    </div>

    <div class="bmi-banner__card bmi-banner__premium-card" id="bmi-banner__card-premium-storage" style="display: none;">
      <div class="bmi-banner__img-wrapper">
        <img src="<?php echo $this->_asset('imgs/5gb-free.svg'); ?>" alt="5 GB premium icon" />
      </div>
      <div class="bmi-banner__card-content">
        <div class="bmi-banner__card-header">
          <span>
            5 GB of <div class="bmi-banner__free-underlined"><span>free</span></div> storage
          </span>
        </div>
        <div class="bmi-banner__card-text">
          <span>
            We added our <b>own storage option,</b> giving you 5 GB of free space as premium user!
            <a href="<?php echo $bbStorage; ?>" target="_blank" class="bmi-links">Check it out</a>
          </span>
        </div>
      </div>
    </div>






    <!-- Card 3 -->
    <div class="bmi-banner__card" id="bmi-banner__card-4gb-upgraded" style="display: none;">
      <div class="bmi-banner__card-header">
        <img src="<?php echo $this->_asset('imgs/4gb-upgraded.svg'); ?>" alt="4 GB double backup size" />
        <span>Double <br> backup size</span>
      </div>
      <span class="bmi-banner__card-text">
        We doubled the supported backup size in the free
        plugin <b>from 2 GB to 4 GB!</b> (Unlimited in
        <a href="<?php echo $bmiPremium; ?>" target="_blank" class="bmi-links">premium</a>)
      </span>
    </div>
  </div>

  <div class="bmi-banner__footer">
    <div class="bmi-banner__footer-text">
    </div>
    <div>
      <div class="install-now-wrapper">
        <button class="bmi-banner__cta-button redirect-to-bmi" style="display: none;">Try it out</button>
        <button class="bmi-banner__cta-button install-bmi" style="display: none;">Install it now</button>
        <span class="install-now-text"> (from <a href="https://wordpress.org/plugins/backup-backup/" target="_blank"
            class="bmi-links">WP directory</a>)</span>
      </div>
    </div>
  </div>
  <a href="#" class="bmi-banner__dismiss-link">Dismiss this forever</a>
</div>