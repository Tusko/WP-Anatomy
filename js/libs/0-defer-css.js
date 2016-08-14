var lds = function () {
    "use strict";
    var wf_asn = document.getElementById("wf_ds"),
        r = document.createElement("div");
    r.innerHTML = wf_asn.textContent;
    document.body.appendChild(r);
    wf_asn.parentElement.removeChild(wf_asn);
};
var f = requestAnimationFrame || mozRequestAnimationFrame || webkitRequestAnimationFrame || msRequestAnimationFrame;
if (f) f(function () {
    window.setTimeout(lds, 1);
});
else window.addEventListener("load", lds);