/* Finance Automation Forms kdr — Frontend JS */

(function ($) {
  "use strict";

  function updateIndexes($table) {
    $table.find("tbody tr").each(function (i) {
      $(this)
        .find("input, select, textarea")
        .each(function () {
          var name = $(this).attr("name");
          if (!name) return;

          name = name.replace(/items\[\d+\]/, "items[" + i + "]");
          $(this).attr("name", name);
        });
    });
  }

  function recalcBilling() {
    // If there is no items table, do nothing.
    if ($(".fafkdr-items").length === 0) return;

    var total = 0;
    var taxTotal = 0;

    $(".fafkdr-items tbody tr").each(function () {
      var $row = $(this);

      var qty = parseFloat($row.find('input[name*="[qty]"]').val()) || 0;
      var rate = parseFloat($row.find('input[name*="[rate]"]').val()) || 0;
      var tax = parseFloat($row.find('input[name*="[tax]"]').val()) || 0;

      var line = qty * rate;
      var lineTax = (line * tax) / 100;

      total += line;
      taxTotal += lineTax;

      var $display = $row.find('input[name*="[line_total_display]"]');
      if ($display.length) {
        $display.val(line.toFixed(2));
      }
    });

    // Update summary if elements exist (safe for other forms).
    var $totalEl = $('[data-fafkdr-total="total"]');
    var $taxEl = $('[data-fafkdr-total="tax"]');
    var $grandEl = $('[data-fafkdr-total="grand"]');

    if ($totalEl.length) $totalEl.text(total.toFixed(2));
    if ($taxEl.length) $taxEl.text(taxTotal.toFixed(2));
    if ($grandEl.length) $grandEl.text((total + taxTotal).toFixed(2));
  }

  // Add new item row (works for billing/invoice/quotation tables).
  $(document).on("click", ".fafkdr-add-row", function () {
    var $table = $(this).closest(".fafkdr-card").find(".fafkdr-items");
    if ($table.length === 0) return;

    var $last = $table.find("tbody tr:last");
    var $clone = $last.clone();

    // Clear values, keep defaults reasonable.
    $clone.find("input").each(function () {
      var $input = $(this);
      if ($input.attr("readonly")) return;

      // Keep qty default 1 for usability if field is qty.
      var name = $input.attr("name") || "";
      if (name.indexOf("[qty]") !== -1) {
        $input.val("1");
      } else if (name.indexOf("[tax]") !== -1) {
        $input.val("0");
      } else if (name.indexOf("[rate]") !== -1) {
        $input.val("0");
      } else {
        $input.val("");
      }
    });

    $clone.find("textarea").val("");
    $clone.find("select").prop("selectedIndex", 0);

    $table.find("tbody").append($clone);
    updateIndexes($table);

    // Recalc after DOM updates.
    setTimeout(recalcBilling, 0);
  });

  // Remove item row
  $(document).on("click", ".fafkdr-remove-row", function () {
    var $table = $(this).closest(".fafkdr-items");
    if ($table.length === 0) return;

    if ($table.find("tbody tr").length <= 1) return;

    $(this).closest("tr").remove();
    updateIndexes($table);

    setTimeout(recalcBilling, 0);
  });

  // Live recalculation (for billing preview; safe for other forms too).
  $(document).on("input", ".fafkdr-items input, .fafkdr-items select", function () {
    recalcBilling();
  });

  // Initial calc on load
  $(document).ready(function () {
    recalcBilling();
  });
})(jQuery);