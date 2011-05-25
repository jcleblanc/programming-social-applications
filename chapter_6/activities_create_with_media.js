//create a new media item for an image
var imageUrl = "http://www.mysite.com/image.jpg";
var mediaImg = opensocial.newMediaItem(“image/jpeg”, imageUrl);
var mediaObj = [mediaImg];

//build parameter list for the activity        
var params = {};
params[opensocial.Activity.Field.TITLE] = "Posting my image";
params[opensocial.Activity.Field.URL] = "http://www.myserver.com/index.php";
params[opensocial.Activity.Field.BODY] = "Testing <b>1 2 3</b>";
params[opensocial.Activity.Field.MEDIA_ITEMS] = mediaObj;
var activityObj = opensocial.newActivity(params);
 
//make request to create a new activity
osapi.activities.create({
   userId: "@viewer", 
   activity: activityObj
}).execute();

