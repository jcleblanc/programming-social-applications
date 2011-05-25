//insert new activity for the current viewer with a high priority
osapi.activities.create({
   userId: “@viewer”, 
   activity: {
      title: “Get more information on my blog”, 
      url: “http://www.nakedtechnologist.com/”,
      priority: 1
   }
}).execute();

