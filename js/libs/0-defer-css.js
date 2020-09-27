var cssDeffered = function() {
  "use strict";
  var cssD = jQuery.Deferred(),
    cssData = document.getElementById("wf_ds"),
    link = document.createElement("div");
  link.id = 'css-defer-load';
  link.innerHTML = cssData.textContent;
  document.body.appendChild(link);
  cssData.parentElement.removeChild(cssData);
  cssD.resolve(true);
  return cssD.promise();
};