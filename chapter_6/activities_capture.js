//capture viewer activities
osapi.activities.get({userId: '@viewer', count: 20}).execute(function(result){
    if (!result.error){
        var activities = result.list;
        var html = '';

        //build title and url for each discovered activity
        for (var i = 0; i < activities.length; i++){
            html += 'Activity Title: ' + activities[i].title +
                    'Activity URL: ' + activities[i].url;
        }
    }
});

