// Always close the code, cause you can make conflicts (same for css use prefixes)
(function ($) {


  let nonce = new_bb_banner.dismiss_nonce;
  let is_backup_pro_exists = new_bb_banner.is_backup_pro_exists;
  let current_plugin = new_bb_banner.current_plugin;
  let is_bmi_exists = new_bb_banner.is_bmi_exists;
  let plugin_page = new_bb_banner.plugin_page;

  if (current_plugin == 'copy-delete-posts') {

    $('#bmi-banner__card-fast-backup').show();
    $('#bmi-banner__card-free-external-storage').show();
    $('#bmi-banner__card-free-storage').show();

    if (is_bmi_exists) {
      $('.redirect-to-bmi').show();
    } else {
      $('.install-bmi').show();
    }
  }


  function banner_hide() {
    $('#new-bb-banner').hide(300);
  }

  function setLoading($btn, text) {
    $btn.data('working', true);
    $btn.addClass('is-loading').attr('disabled', 'disabled');
    if (text) $btn.text(text);
  }

  function setRedirecting($btn) {
    $btn.removeClass('is-loading').addClass('is-redirecting').attr('disabled', 'disabled').text('Redirecting...');
  }

  function safeRedirect(url) {
    if (!url) return;
    try {
      const currentUrl = new URL(window.location.href, window.location.origin);
      const targetUrl = new URL(url, window.location.origin);
      if (
        currentUrl.pathname === targetUrl.pathname &&
        currentUrl.search === targetUrl.search
      ) return;
    } catch (e) {
      if (window.location.href.indexOf(url) !== -1) return;
    }
    window.location.href = url;
  }

  $('.bmi-banner__cta-button.redirect-to-bmi').on('click', function (e) {
    e.preventDefault();
    const $btn = $(this);
    if ($btn.data('working')) return;
    setLoading($btn, 'Redirecting...');

    $.post(ajaxurl, {
      action: 'activate_bmi',
      nonce: nonce,
      token: 'new_bb_banner'
    }).done(function (res) {
      try { if (typeof res === 'string') res = JSON.parse(res); } catch (e) { }
      setRedirecting($btn);
      const target = res && res.success && res.data && res.data.redirect ? res.data.redirect : 'admin.php?page=backup-migration';
      dismiss_new_bb_banner(function () {
        safeRedirect(target);
      });
    }).fail(function () {
      setRedirecting($btn);
      setTimeout(function () { safeRedirect('admin.php?page=backup-migration'); }, 500);
    });
  });

  $('.bmi-banner__cta-button.install-bmi').on('click', function (e) {
    e.preventDefault();
    const $btn = $(this);
    if ($btn.data('working')) return;
    setLoading($btn, 'Installing...');

    $.post(ajaxurl, {
      action: 'install_bmi',
      nonce: nonce,
      token: 'new_bb_banner'
    }).done(function (res) {
      try { if (typeof res === 'string') res = JSON.parse(res); } catch (e) { }
      setRedirecting($btn);
      const target = res && res.success && res.data && res.data.redirect ? res.data.redirect : 'admin.php?page=backup-migration';
      dismiss_new_bb_banner(function () {
        safeRedirect(target);
      });
      setTimeout(function () { safeRedirect(target); }, 500);
    }).fail(function () {
      setRedirecting($btn);
      setTimeout(function () { safeRedirect('admin.php?page=backup-migration'); }, 500);
    });
  });

  $('.bmi-banner__dismiss-link, .bmi-banner__close').on('click', dismiss_new_bb_banner);

  function dismiss_new_bb_banner(e, callback) {
    if (typeof e === 'function') {
      callback = e;
      e = null;
    }

    if (e && typeof e.preventDefault === 'function') {
      e.preventDefault();
    }    
    $.post(ajaxurl, {
      action: 'dismiss_new_bb_banner',
      nonce: nonce,
      token: 'new_bb_banner'
    }).done(function (res) {
      banner_hide();
      if (callback) callback();
    }).fail(function (err) {
      console.error(err);
      banner_hide();
      if (callback) callback();
    });
  }

})(jQuery);
