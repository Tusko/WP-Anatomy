!function(a) {
  "use strict";

  function b() {
    e = !1;
    for(var b = 0; b < c.length; b++) {
      var d = a(c[b]).filter(function() {
        return a(this).is(":appeared")
      });
      if(d.trigger("appear", [d]), h) {
        var f = h.not(d);
        f.trigger("disappear", [f])
      }
      h = d
    }
  }

  var h,
    c = [],
    d = !1,
    e = !1,
    f = {
      interval: 250,
      force_process: !1
    },
    g = a(window);
  a.expr[":"].appeared = function(b) {
    var c = a(b);
    if(!c.is(":visible")) return !1;
    var d = g.scrollLeft(),
      e = g.scrollTop(),
      f = c.offset(),
      h = f.left,
      i = f.top;
    return i + c.height() >= e && i - (c.data("appear-top-offset") || 0) <= e + g.height() && h + c.width() >= d && h - (c.data("appear-left-offset") || 0) <= d + g.width()
  }, a.fn.extend({
    appear: function(g) {
      var h = a.extend({}, f, g || {}),
        i = this.selector || this;
      if(!d) {
        var j = function() {
          e || (e = !0, setTimeout(b, h.interval))
        };
        a(window).scroll(j).resize(j), d = !0
      }
      return h.force_process && setTimeout(b, h.interval), c.push(i), a(i)
    }
  }), a.extend({
    force_appear: function() {
      return !!d && (b(), !0)
    }
  })
}(jQuery),
  function(a) {
    "$:nomunge";

    function b(b) {
      function d() {
        b ? h.removeData(b) : m && delete c[m]
      }

      function f() {
        i.id = setTimeout(function() {
          i.fn()
        }, n)
      }

      var h,
        g = this,
        i = {},
        j = b ? a.fn : a,
        k = arguments,
        l = 4,
        m = k[1],
        n = k[2],
        o = k[3];
      if("string" != typeof m && (l--, m = b = 0, n = k[1], o = k[2]), b ? (h = g.eq(0), h.data(b, i = h.data(b) || {})) : m && (i = c[m] || (c[m] = {})), i.id && clearTimeout(i.id), delete i.id, o) i.fn = function(a) {
        "string" == typeof o && (o = j[o]), o.apply(g, e.call(k, l)) !== !0 || a ? d() : f()
      }, f();
      else {
        if(i.fn) return void 0 === n ? d() : i.fn(n === !1), !0;
        d()
      }
    }

    var c = {},
      d = "doTimeout",
      e = Array.prototype.slice;
    a[d] = function() {
      return b.apply(window, [0].concat(e.call(arguments)))
    }, a.fn[d] = function() {
      var a = e.call(arguments),
        c = b.apply(this, [d + a[0]].concat(a));
      return "number" == typeof a[0] || "number" == typeof a[1] ? this : c
    }
  }(jQuery), $(".animatedParent").appear(), $(".animatedClick").click(function() {
  var a = $(this).attr("data-target");
  if(void 0 != $(this).attr("data-sequence")) {
    var b = $("." + a + ":first").attr("data-id"),
      c = $("." + a + ":last").attr("data-id"),
      d = b,
      delay = Number(typeof $(this).attr("data-sequence") !== undefined ? $(this).attr("data-sequence") : 500);
    $("." + a + "[data-id=" + d + "]").hasClass("go") ? ($("." + a + "[data-id=" + d + "]").addClass("goAway"), $("." + a + "[data-id=" + d + "]").removeClass("go")) : ($("." + a + "[data-id=" + d + "]").addClass("go"), $("." + a + "[data-id=" + d + "]").removeClass("goAway")), d++, $.doTimeout(delay, function() {
      if($("." + a + "[data-id=" + d + "]").hasClass("go") ? ($("." + a + "[data-id=" + d + "]").addClass("goAway"), $("." + a + "[data-id=" + d + "]").removeClass("go")) : ($("." + a + "[data-id=" + d + "]").addClass("go"), $("." + a + "[data-id=" + d + "]").removeClass("goAway")), ++d, d <= c) return !0
    })
  } else $("." + a).hasClass("go") ? ($("." + a).addClass("goAway"), $("." + a).removeClass("go")) : ($("." + a).addClass("go"), $("." + a).removeClass("goAway"))
}), $(document.body).on("appear", ".animatedParent", function(a, b) {
  var c = $(this).find(".animated"),
    d = $(this);
  if(void 0 != d.attr("data-sequence")) {
    var e = $(this).find(".animated:first").attr("data-id"),
      f = e,
      g = $(this).find(".animated:last").attr("data-id"),
      delay = Number(typeof $(this).attr("data-sequence") !== undefined ? $(this).attr("data-sequence") : 500);
    $(d).find(".animated[data-id=" + f + "]").addClass("go"), f++, $.doTimeout(delay, function() {
      if($(d).find(".animated[data-id=" + f + "]").addClass("go"), ++f, f <= g) return !0
    })
  } else c.addClass("go")
}), $(document.body).on("disappear", ".animatedParent", function(a, b) {
  $(this).hasClass("animateOnce") || $(this).find(".animated").removeClass("go")
}), $(window).on("defer.cssLoad", function() {
  $.force_appear()
});
