var map, updateConn, trendsConn, mapsize = {"x": 1000, "y": 400},
    tooltip = $('#tooltip'),
    countryPathIds = {},
    paper;

window.onmousemove = function (e) {
    var x = e.clientX,
        y = e.clientY;
    tooltip.css('top', (y + 20) + 'px');
    tooltip.css('left', (x + 20) + 'px');
};

$(window).on('beforeunload', function(){
    updateConn.close();
    trendsConn.close();
});

updateConn = new WebSocket('ws://' + window.location.host + ':8080/update');
trendsConn = new WebSocket('ws://' + window.location.host + ':8080/trends');

updateConn.onopen = function(e) {
    console.log("Update connection established!");
};
trendsConn.onopen = function(e) {
    console.log("Trends connection established!");
};

updateConn.onmessage = function(e) {
    var payload;

    console.log("Update Message");
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

trendsConn.onmessage = function(payload) {
    tooltip.html("");
    try {
        payload = JSON.parse(payload.data);
    } catch (e) {
        console.log("Non-JSON string as data provided.");
        console.log(payload);
        return;
    }

    if(payload.status == "error") {
        tooltip.html(payload.error);
        return;
    }

    // sorting pairs by trades count
    var sortable = [], tpl = "";
    for(pair in payload.countryData.topPairs) {
        sortable.push([pair, payload.countryData.topPairs[pair]]);
    }
    sortable.sort(function(a, b) {return a[1] - b[1]}).slice(0, 5);
    for(pair in sortable) {
        tpl += sortable[pair][0] + ": " + sortable[pair][1] + "<br/>"
    }

    tooltip.html("Country code: " + payload.countryData.code + "<br/>" +
    "Trades count: " + payload.countryData.tradesCount + "</br>" +
    "Top 5 trade pairs: <hr>" +
    tpl);
}

Raphael(document.getElementById("map"), "100%", "100%", "100%", "100%", function () {
    var r = paper = this;
    var countryPath;

    r.rect(0, 0, "100%", "100%", 10).attr({
        stroke: "none",
        fill: "0-#9bb7cb-#adc8da"
    });

    r.setStart();

    for (var country in worldmap.shapes) {
        countryPath = r.path(worldmap.shapes[country]).attr({
            stroke: "#ccc6ae",
            fill: "#f0efeb",
            "stroke-opacity": 0.3})
            .data("country", country);

        countryPathIds[country] = countryPath.id;
    }
    var world = r.setFinish();

    var over = function() {
        trendsConn.send(JSON.stringify({'country': flip(countryPathIds)[this.id]}));
        tooltip.addClass("tooltip-hover");
        this.c = this.c || this.attr("fill");
        this.stop().animate({fill: "#bacabd"}, 500);
    }

    var out = function() {
        tooltip.removeClass("tooltip-hover");
        this.stop().animate({fill: this.c}, 500);
    }

    world.hover(over, out);

    world.scale($("#map").width() / mapsize.x, $("#map").height() / mapsize.y, 0, -25);
});

var flip = function (obj) {

    var new_obj = {};

    for (var prop in obj) {
        if(obj.hasOwnProperty(prop)) {
            new_obj[obj[prop]] = prop;
        }
    }

    return new_obj;
};