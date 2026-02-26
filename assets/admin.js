/* Finance Automation Forms kdr — Admin JS */

  (function () {
  function copyText(text) {
    if (!text) return Promise.reject();
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    // Fallback
    return new Promise(function (resolve, reject) {
      var ta = document.createElement("textarea");
      ta.value = text;
      ta.setAttribute("readonly", "");
      ta.style.position = "absolute";
      ta.style.left = "-9999px";
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand("copy");
        document.body.removeChild(ta);
        resolve();
      } catch (e) {
        document.body.removeChild(ta);
        reject(e);
      }
    });
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-fafkdr-copy-btn]");
    if (!btn) return;

    var code = btn.getAttribute("data-fafkdr-copy-btn") || "";
    var original = btn.textContent;

    copyText(code)
      .then(function () {
        btn.textContent = "Copied!";
        btn.classList.add("button-primary");
        setTimeout(function () {
          btn.textContent = original || "Copy";
          btn.classList.remove("button-primary");
        }, 900);
      })
      .catch(function () {
        btn.textContent = "Copy failed";
        setTimeout(function () {
          btn.textContent = original || "Copy";
        }, 1200);
      });
  });

  // Also allow clicking on the shortcode itself
  document.addEventListener("click", function (e) {
    var el = e.target.closest("[data-fafkdr-copy]");
    if (!el) return;
    var code = el.getAttribute("data-fafkdr-copy") || "";
    copyText(code).catch(function () {});
  });
})(jQuery);

