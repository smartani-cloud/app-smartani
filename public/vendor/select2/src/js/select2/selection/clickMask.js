<<<<<<< HEAD
define([
  'jquery'
], function ($) {
  function ClickMask () { }

  ClickMask.prototype.bind = function (decorate, $container, container) {
    var self = this;

    decorate.call(this, $container, container);

    this.$mask = $(
      '<div class="select2-close-mask"></div>'
    );

    this.$mask.on('mousedown touchstart click', function () {
      self.trigger('close', {});
    });
  };

  ClickMask.prototype._attachCloseHandler = function (decorate, container) {
    $(document.body).append(this.$mask);
  };

  ClickMask.prototype._detachCloseHandler = function (deocrate, container) {
    this.$mask.detach();
  };

  return ClickMask;
});
=======
define([
  'jquery'
], function ($) {
  function ClickMask () { }

  ClickMask.prototype.bind = function (decorate, $container, container) {
    var self = this;

    decorate.call(this, $container, container);

    this.$mask = $(
      '<div class="select2-close-mask"></div>'
    );

    this.$mask.on('mousedown touchstart click', function () {
      self.trigger('close', {});
    });
  };

  ClickMask.prototype._attachCloseHandler = function (decorate, container) {
    $(document.body).append(this.$mask);
  };

  ClickMask.prototype._detachCloseHandler = function (deocrate, container) {
    this.$mask.detach();
  };

  return ClickMask;
});
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
