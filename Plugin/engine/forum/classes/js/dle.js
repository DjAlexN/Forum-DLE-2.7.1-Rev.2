function ShowLoading(a) {
    a && $("#loading-layer-text").html(a);
    a = ($(window).width() - $("#loading-layer").width()) / 2;
    var b = ($(window).height() - $("#loading-layer").height()) / 2;
    $("#loading-layer").css({ left: a + "px", top: b + "px", position: "fixed", zIndex: "99" });
    $("#loading-layer").fadeTo("slow", 0.6);
}
function HideLoading() {
    $("#loading-layer").fadeOut("slow");
}
