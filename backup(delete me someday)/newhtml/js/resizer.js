$(function() {
    var theWindow = $(window);
 
    function resize() {
        var windowHeight = document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight;
        $("#main").css("height", (windowHeight - 35 - 40) + "px");
        $("#sub2").css("height", (windowHeight - 35 - 160) + "px");
    }
    theWindow.resize(function() {
        resize();
    }).trigger("resize");
});
