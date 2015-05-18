<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HTML5 boilerplate – all you really need…</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        html {
            height: 100%;
        }

        #home {
            background: #EFF2FB;
            text-align: center;
            height: 100%;
        }

        #map {
            width: 95%;
            height: 90%;
            margin-top:2.5%;
            display: inline-block;
        }

        #mapdiv {
            width:95%;
            height:95%;
            margin-top:1%;
        }
    </style>
    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="http://currencyfair.lan/js/raphael/raphael.js"></script>
    <script src="http://currencyfair.lan/js/raphael/world.js"></script>

</head>

<body id="home">

<div id="map">

</div>

<script>

    $(window).on('beforeunload', function(){
        conn.close();
    });

    var map, conn, mapsize = {"x": 1000, "y": 400};

    conn = new WebSocket('ws://currencyfair.lan:8080/update');

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var payload;

        console.log("Message");
        try {
            payload = JSON.parse(e.data);
        } catch (e) {
            console.log("Non-JSON string as data provided.")
            return;
        }

        paper.getById(countryPathIds[payload.originatingCountry])
            .attr({fill: "#bacabd"})
            .animate({fill: "#f0efeb"}, 300);
    };

    var countryPathIds = {};
    var paper;

    Raphael(document.getElementById("map"), "100%", "100%", "100%", "100%", function () {
        var r = paper = this;

        r.rect(0, 0, "100%", "100%", 10).attr({
            stroke: "none",
            fill: "0-#9bb7cb-#adc8da"
        });

        r.setStart();
        var countryPath;

        for (var country in worldmap.shapes) {
            countryPath = r.path(worldmap.shapes[country]).attr({
                    stroke: "#ccc6ae",
                    fill: "#f0efeb",
                    "stroke-opacity": 0.3})
                .data("country", country);

            countryPathIds[country] = countryPath.id;
        }
        var world = r.setFinish();
        //world.scale(1.4,1.8,0,0)
    });

</script>

</body>
</html>