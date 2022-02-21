+(function ($) {
  "use strict";

  $(document).ready(function () {
    $(".choose_tab_ajax").change(function () {
      let valTab = $(this).val();
      let idTab = $(this).attr("id"); // Ex : #choose_tab_1a
     
      let splitId = idTab.split("_");
      let tab = splitId[splitId.length - 1]; // on récupere que la fin de l'id. Ex : 1a

      let valiTab = $("#choose_itab_" + tab).val();     
      let valIDTab = $("#tab_id").val();

      if (valTab != "") {
        $.ajax({
          url: "index.php?controller=pref&action=tabAjax",
          method: "post",
          data: {
            tab: tab,
            tab_n: valTab,
            itab_n: valiTab,
            tab_id: valIDTab,
          },
          contentType: "application/x-www-form-urlencoded; charset=UTF-8",
          success: function (response) {
            $("#result_tab_" + tab).html(response);
          },
        });
      }
    });
  });
})(window.jQuery);
