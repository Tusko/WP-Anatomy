!function (c) {
  "use strict";

  function e(e, t, n, o) {
    var r,
      i,
      d = c.document,
      a = d.createElement("link");
    i = t || (r = (d.body || d.getElementsByTagName("head")[0]).childNodes)[r.length - 1];
    var f = d.styleSheets;
    if (o) for (var l in o) o.hasOwnProperty(l) && a.setAttribute(l, o[l]);
    a.rel = "stylesheet", a.href = e, a.media = "only x", function e(t) {
      if (d.body) return t();
      setTimeout(function () {
        e(t)
      })
    }(function () {
      i.parentNode.insertBefore(a, t ? i : i.nextSibling)
    });
    var s = function (e) {
      for (var t = a.href, n = f.length; n--;) if (f[n].href === t) return e();
      setTimeout(function () {
        s(e)
      })
    };

    function u() {
      a.addEventListener && a.removeEventListener("load", u), a.media = n || "all"
    }

    return a.addEventListener && a.addEventListener("load", u), (a.onloadcssdefined = s)(u), a
  }

  "undefined" != typeof exports ? exports.loadCSS = e : c.loadCSS = e
}("undefined" != typeof global ? global : this);

function onloadCSS(n, a) {
  var t;

  function d() {
    !t && a && (t = !0, a.call(n))
  }

  n.addEventListener && n.addEventListener("load", d), n.attachEvent && n.attachEvent("onload", d), "isApplicationInstalled" in navigator && "onloadcssdefined" in n && n.onloadcssdefined(d)
}

var cssDeffered = function () {
  "use strict";

  var cssD = jQuery.Deferred(),
    cssPreload = document.getElementById("__wp_defer_css"),
    cssLoad = loadCSS(cssPreload.getAttribute('href'));

  onloadCSS(cssLoad, function () {
    cssD.resolve(true);
  });

  return cssD.promise();
};