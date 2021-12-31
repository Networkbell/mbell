+(function ($) {
  "use strict";

  var inactiveClass = "#drap a.inactive",
    active = "#drap",
    submenuClass = ".pushy-submenu",
    submenuOpenClass = "pushy-submenu-open",
    submenuClosedClass = "pushy-submenu-closed",
    submenu = $(submenuClass),
    inactive = $(inactiveClass);

  submenu.addClass(submenuClosedClass);

  submenu.on("click", function () {
    var selected = $(this);

    if (selected.hasClass(submenuClosedClass)) {
      //hide opened submenus
      submenu.addClass(submenuClosedClass).removeClass(submenuOpenClass);
      //show submenu
      selected.removeClass(submenuClosedClass).addClass(submenuOpenClass);
    } else {
      //hide submenu
      selected.addClass(submenuClosedClass).removeClass(submenuOpenClass);
    }
  });

  inactive.css("opacity", 0.2);

  $(active).on({
    mouseenter: function () {
      inactive.stop().fadeTo(400, 2);
    },
    mouseleave: function () {
      inactive.animate({ opacity: 0.2 }, 2000);
    },
  });

  inactive.on({
    mouseenter: function () {
      inactive.stop().fadeTo(400, 1);
    },
    mouseleave: function () {
      inactive.animate({ opacity: 0.2 }, 2000);
    },
  });

 
  //active les tooltip de bootstrap
$('[data-toggle="tooltip"]').tooltip();	


  /* < TITRES > H1 dans PREF */
  $("#toggle0").click(function () {
    $("#show_hide0").fadeToggle("slow", "linear");
    $("#i_downleft0").fadeToggle("fast");
    $("#i_downright0").fadeToggle("fast");
    $("#i_left0").fadeToggle("fast");
    $("#i_right0").fadeToggle("fast");
    $("#title0").toggleClass("border_bottom");
    $("#show_hide0").toggleClass("border_bottom");
  });

  $("#toggle1").click(function () {
    $("#show_hide1").fadeToggle("slow", "linear");
    $("#i_downleft1").fadeToggle("fast");
    $("#i_downright1").fadeToggle("fast");
    $("#i_left1").fadeToggle("fast");
    $("#i_right1").fadeToggle("fast");
  });

  $("#toggle2").click(function () {
    $("#show_hide2").fadeToggle("slow", "linear");
    $("#i_downleft2").fadeToggle("fast");
    $("#i_downright2").fadeToggle("fast");
    $("#i_left2").fadeToggle("fast");
    $("#i_right2").fadeToggle("fast");
    $("#title2").toggleClass("border_bottom");
    $("#show_hide2").toggleClass("border_bottom");
  });

  $("#toggle3").click(function () {
    $("#show_hide3").fadeToggle("slow", "linear");
    $("#i_downleft3").fadeToggle("fast");
    $("#i_downright3").fadeToggle("fast");
    $("#i_left3").fadeToggle("fast");
    $("#i_right3").fadeToggle("fast");
    $("#title3").toggleClass("border_bottom");
    $("#show_hide3").toggleClass("border_bottom");
  });

  $("#toggle4").click(function () {
    $("#show_hide4").fadeToggle("slow", "linear");
    $("#i_downleft4").fadeToggle("fast");
    $("#i_downright4").fadeToggle("fast");
    $("#i_left4").fadeToggle("fast");
    $("#i_right4").fadeToggle("fast");
  });

  $("#toggle5").click(function () {
    $("#show_hide5").fadeToggle("slow", "linear");
    $("#i_downleft5").fadeToggle("fast");
    $("#i_downright5").fadeToggle("fast");
    $("#i_left5").fadeToggle("fast");
    $("#i_right5").fadeToggle("fast");
  });

  $("#show_hide0").hide();
  $("#show_hide1").hide();
  $("#show_hide2").hide();
  $("#show_hide3").hide();
  $("#show_hide4").hide();
  $("#show_hide5").hide();
  $("#title0").addClass("border_bottom");
  $("#title2").addClass("border_bottom");
  $("#title3").addClass("border_bottom");

  $("#i_downleft0").hide();
  $("#i_downright0").hide();
  $("#i_downleft1").hide();
  $("#i_downright1").hide();
  $("#i_downleft2").hide();
  $("#i_downright2").hide();
  $("#i_downleft3").hide();
  $("#i_downright3").hide();
  $("#i_downleft4").hide();
  $("#i_downright4").hide();
  $("#i_downleft5").hide();
  $("#i_downright5").hide();

  $(document).ready(function () {
    /*$("body").hide().fadeIn(1000);	*/

    /*SELECT AUTO-SUBMIT */
    $("#choose_lang").change(function () {
      this.form.submit();
    });

    $("#choose_station").change(function () {
      this.form.submit();
    });

    $(".var_lines").click(function () {
      this.form.submit();
    });

    $("#cron_btn").click(function () {
      setTimeout(function(){
        location.reload(true);
        },1000);   
    });
 

    $(".dmy_left")
      .on("mouseover", function () {
        $(".dmy_left").addClass("hover_left");
        $(".arrow_left").addClass("h_arrow_left");
      })
      .on("mouseleave", function () {
        $(".dmy_left").removeClass("hover_left");
        $(".arrow_left").removeClass("h_arrow_left");
      });

    $(".dmy_right")
      .on("mouseover", function () {
        $(".dmy_right").addClass("hover_right");
        $(".arrow_right").addClass("h_arrow_right");
      })
      .on("mouseleave", function () {
        $(".dmy_right").removeClass("hover_right");
        $(".arrow_right").removeClass("h_arrow_right");
      });

    $(".dmy_arrow_left")
      .on("mouseover", function () {
        $(".dmy_arrow_left").addClass("hover_arrow_left");
        $(".dmy_display_on").addClass("dmy_display_on_hover");
        $(".dmy_display_off").addClass("dmy_display_off_hover");
      })
      .on("mouseleave", function () {
        $(".dmy_arrow_left").removeClass("hover_arrow_left");
        $(".dmy_display_on").removeClass("dmy_display_on_hover");
        $(".dmy_display_off").removeClass("dmy_display_off_hover");
      });

    $(".dmy_arrow_right")
      .on("mouseover", function () {
        $(".dmy_arrow_right").addClass("hover_arrow_right");
        $(".dmy_display_on").addClass("dmy_display_on_hover");
        $(".dmy_display_off").addClass("dmy_display_off_hover");
      })
      .on("mouseleave", function () {
        $(".dmy_arrow_right").removeClass("hover_arrow_right");
        $(".dmy_display_on").removeClass("dmy_display_on_hover");
        $(".dmy_display_off").removeClass("dmy_display_off_hover");
      });
  });
})(window.jQuery);
