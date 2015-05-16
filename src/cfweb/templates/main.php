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
            background: white;
            border: 1px solid grey;
            display: inline-block;
            border-radius: 5px;
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
    <script src="http://currencyfair.lan/js/ammap/ammap/ammap.js"></script>
    <script src="http://currencyfair.lan/js/ammap/ammap/maps/js/worldLow.js"></script>
    <script>

        $(window).on('beforeunload', function(){
            conn.close();
        });

        var map, conn;

        function writeDevInfo(event) {
            console.log(map.dataProvider);
            console.log(map.getObjectById('RO'));
        }

        AmCharts.ready(function() {
            map = new AmCharts.AmMap();
            map.path = "/js/ammap/ammap/";

            map.balloon.color = "#000000";
            var dataProvider = {
                mapVar: AmCharts.maps.worldLow,
                getAreasFromMap: true
            };

            map.dataProvider = dataProvider;
            map.zoomControl.zoomControlEnabled = false;
            map.zoomControl.panControlEnabled = false;

            // developer mode related
            map.developerMode = true;
            map.mouseWheelZoomEnabled = false;
            map.zoomOnDoubleClick = false;
            map.showBalloonOnSelectedObject = false;

            map.areasSettings.balloonText = false;
            map.areasSettings.color = '#cccccc';
            map.addListener("click", writeDevInfo);

            map.write("mapdiv");
        });

        function colorRandomCountry() {
            var country = map.getObjectById(pickRandomProperty(map.svgAreasById));
            country.color = "#"+((1<<24)*Math.random()|0).toString(16);
            map.validateData();
        }

        function pickRandomProperty(obj) {
            var result;
            var count = 0;
            for (var prop in obj)
                if (Math.random() < 1/++count)
                    result = prop;
            return result;
        }

        conn = new WebSocket('ws://currencyfair.lan:8080/update');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
        };
    </script>
</head>

<body id="home">

    <div id="map">
        <div id="mapdiv">

        </div>
    </div>

</body>
</html>