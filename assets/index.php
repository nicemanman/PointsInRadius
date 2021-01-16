<?php


?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=05cde97b-8201-4758-b5c9-144fcb5cf043&lang=ru_RU"
            type="text/javascript"></script>
    <title>Тестируем геоданные</title>
</head>
<body>
<center>

    <div id="map" style="width: 1200px; height: 640px"></div>
    <div>
        широта: <input type="text" id="lat" value="55.821642"/>
        долгота: <input type="text" id = "lon" value="37.573778"/>
        радиус: <input type="text" id = "rad" value="2000" />
        
    </div>
    <br>
    <div class="buttons">
        <button id="start">
            Нарисовать точки и окружность
        </button>
        
    </div>
    <div>
        <button id="clear">
            Очистить карту
        </button>
    </div>
    <div class="result"></div>
</center>
</body>

<script type="text/javascript">
    ymaps.ready(init);
    
    
    function init(){
    let radius = document.getElementById("rad").value;
    let lat = document.getElementById("lat").value;
    let lon = document.getElementById("lon").value;
    let centerValue = [lat, lon];
    var pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: radius, center: centerValue, circleColor: '#ff0000'}
        };

    $("#lat").on("change", function(){
       lat = document.getElementById("lat").value;
       centerValue = [lat, lon]
       pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: radius, center: centerValue, circleColor: '#ff0000'}
        };
    });
    $("#lon").on("change", function(){
       lon = document.getElementById("lon").value;
       centerValue = [lat, lon]
       pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: radius, center: centerValue, circleColor: '#ff0000'}
        };
    });
    $("#rad").on("change", function(){
       radius = document.getElementById("rad").value;
       pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: radius, center: centerValue, circleColor: '#ff0000'}
        };
    });

        var myMap = new ymaps.Map("map", {
            center: [55.753215, 37.622504],
            zoom: 9
        });
        
        


        var collections = {};



        (function () {
            const longRadiusCircle = [
                {color: 'red', radius: 100000, center: [55.762121, 39.204793], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [54.851360, 37.665257], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [56.273250, 36.314308], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [55.351073, 36.196440], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [56.650848, 37.644335], circleColor: '#ff0000'}
            ];

            var pointsSetsSmall = {
                all: {color: 'blue'},
                firstCircle: {}
            };


            $('#start').click(function () {
                myMap.geoObjects.removeAll();
                addCollections();
                $.ajax({

                    url: 'resolve_request.php?lat='+lat+'&lon='+lon+'&radius='+radius,
                    success: function(points){
                        drawPoints(points, pointsSets);
                    }
                });
            });

            $('#errors').click(function () {
                let circleType = $('input[name=typePoints]:checked').val();
                pointsSetsSmall.firstCircle = longRadiusCircle[circleType];
                $.ajax({
                    url: 'newrequest.php',
                    data: {
                        radius: pointsSetsSmall.firstCircle.radius,
                        center: pointsSetsSmall.firstCircle.center
                    },
                    success: function(points){
                        drawPoints(points, pointsSetsSmall);
                    }
                });
            });

            $('#clear').click(function(){
                myMap.geoObjects.removeAll();
                addCollections();
            });


            function drawPoints(points, pointsSets){
                console.log(pointsSets);
                for(let key in pointsSets){
                    let collection = collections[key];
                    let currentSet = points['points'][key];
                    let pointSetData = pointsSets[key];
                    console.log(pointSetData);
                    for (let a in currentSet){
                        let item = currentSet[a];
                        let coords = [item.lat, item.lon];
                        let placemark = new ymaps.Placemark(coords, { balloonContent: 'Точка ' + item.id + 'Координаты '+ coords});
                        
                        collection.add(placemark);
                    }
                    if(!pointSetData.radius){
                        continue;
                    }
                    var myCircle = new ymaps.Circle([
                        pointSetData.center,
                        pointSetData.radius
                    ], {
                        balloonContent: "Круг для контроля попадания точек",
                    }, {
                        fillColor: pointSetData.circleColor,
                        fillOpacity: 0.3,
                        geodesic: true,
                        strokeColor: pointSetData.circleColor,
                        strokeOpacity: 0.8,
                        strokeWidth: 3
                    });

                    myMap.geoObjects.add(myCircle);
                }
                $('.result').html('Время работы скрипта ' + points.time + ' секунд');
            }

            function addCollections(){
                for(let key in pointsSets){
                    col = new ymaps.GeoObjectCollection(null, {preset: 'islands#'+pointsSets[key]['color']+'CircleDotIcon'});
                    collections[key] = col;
                    myMap.geoObjects.add(col);
                }
            }
            addCollections();
        }());
    }


</script>

</html>