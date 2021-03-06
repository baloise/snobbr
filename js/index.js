function makeDynamic(objectThis){
    var href = $(objectThis).attr('href');

    $(objectThis).click(function(event){
        event.preventDefault();
        $("#pageContent").fadeOut(50, function(){
            var newUrl = href.replace('modul/','').replace('.php','');
            window.history.pushState({info: href}, "Title", "?page=" + newUrl.split('/')[0]);
            goBack(href);
        });
    });
}

function goBack(href){
    if (href){

        var newUrl = href.replace('modul/','').replace('.php','');

        var loader = setTimeout(function(){
            $('.loadScreen').fadeTo(50, 1);
        },1000);


        $("#pageContent").load(href, function(response, status, xhr){

            if ( status == "error" ) {

                $('.loadScreen').fadeTo(10, 0);
                var msg = "makeDynamic Error";
                console.log( msg + xhr.status + " " + xhr.statusText );
                window.location.replace("logout.php");

            } else {

                clearTimeout(loader);
                $('.loadScreen').fadeTo(10, 0, function(){
                    $('#pageContent').fadeTo(10, 1);
                });
                $.ajax({
                    method: "GET",
                    url: "includes/setCurrentPath.php",
                    data: {path:href},
                    success: function(){}
                });

            }
        });
    } else {
        $("#pageContent").html("<br/><br/><div class='alert alert-danger'><strong>"+translate[95]+" </strong> "+translate[156]+ ".</div>");
    }
}

$(document).ready(function(){

    $(window).on('popstate',function(event) {

        var href = window.history.state["info"];
        $("#pageContent").fadeOut(10, function(){
            goBack(href);
        });

    });

    $('.navbar-collapse a').click(function(event){
        event.preventDefault;
        $(".navbar-collapse").collapse('hide');
    });

    $("#pageContent").load($("#pageContent").attr("page"), function(){

        window.history.pushState({info: $("#pageContent").attr("page")}, "index.php");

        $('.loadScreen').fadeTo(10, 0, function(){
            $('body').fadeIn(10);
			$("#slideMe").slideDown(10);
			$("#slideMeFoot").slideDown(10);
            $('#pageContents').fadeTo(10, 1);
        });

    });

    $(".foot-link").each(function(){
        makeDynamic(this);
    });

    $(".nav-link").each(function(){
        makeDynamic(this);
    });

    $(".navbar-brand").each(function(){
        makeDynamic(this);
    });

});
