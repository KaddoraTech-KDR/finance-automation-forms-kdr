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

  $(document).on("click", ".fafkdr-add-row", function () {
    var $table = $(this).closest(".fafkdr-card").find(".fafkdr-items");
    var $last = $table.find("tbody tr:last");
    var $clone = $last.clone();

    $clone.find("input").val("");
    $clone.find("textarea").val("");
    $clone.find("select").prop("selectedIndex", 0);

    $table.find("tbody").append($clone);
    updateIndexes($table);
  });

  $(document).on("click", ".fafkdr-remove-row", function () {
    var $table = $(this).closest(".fafkdr-items");

    if ($table.find("tbody tr").length <= 1) return;

    $(this).closest("tr").remove();
    updateIndexes($table);
  });
})(jQuery);