(function () {
  'use strict';

  window.dataLayer = window.dataLayer || [];

  function pushAnalyticsCommand() {
    window.dataLayer.push(arguments);
  }

  window.gtag = pushAnalyticsCommand;
  pushAnalyticsCommand('js', new Date());
  pushAnalyticsCommand('config', 'G-STD5V42M8L');
})();
