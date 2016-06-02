<div class="jumbotron" style="border-radius: 0 0 6px 6px;">

    <?php

    if ($search == true) {
    ?>
    <h1><?=$cityName?></h1>
    <div id="map" style="height: 500px;"></div>

    <script>
        <?php

        if (count($searchResult->results) > 0 && count($twitterResult->statuses) > 0) {
        ?>
        var searchResult = [
            <?php
            $mapPoint = '';

            for ($num = 0; $num < count($twitterResult->statuses); $num++) {

                $tweet = $twitterResult->statuses[$num];

                if ($tweet->coordinates != '')
                    $mapPoint .= '["'.htmlspecialchars($tweet->place->name).'", '.$tweet->coordinates->coordinates[0].', '.$tweet->coordinates->coordinates[1].', '.$num.'],';
            }

            echo substr($mapPoint, 0, -1);

            ?>
        ];
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var labelIndex = 0;

        function initMap() {
            var firstMarker = { lat: <?=$searchResult->results[0]->geometry->location->lat?>, lng: <?=$searchResult->results[0]->geometry->location->lng?> };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: firstMarker
            });

            setMarkers(map);
        }

        function setMarkers(map) {

            for (var i = 0; i < searchResult.length; i++) {
                var data = searchResult[i];
                var marker = new google.maps.Marker({
                    position: {lat: data[1], lng: data[2]},
                    map: map,
                    title: data[0],
                    zIndex: data[3]
                });
            }
        }
        <?php
        }

        ?>
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=<?=API_KEY?>&callback=initMap">
    </script>
    <?php
    }

    ?>

    <div class="col-sm-12">
        <form class="form-horizontal" action="search" method="post">
            <div class="col-sm-11">
                <input class="form-control pull-left" name="city_name" placeholder="City Name" value="<?=(isset($cityName)) ? $cityName : ''?>" required>
            </div>
            <div class="col-sm-1">
                <input type="submit" class="btn btn-sm btn-primary pull-left" value="Search">
            </div>
        </form>
    </div>
</div>