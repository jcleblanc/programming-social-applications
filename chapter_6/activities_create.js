//insert new activity for the current viewer
osapi.activities.create({
   userId: “@viewer”,
   groupId: “@self”,
   activity: {
      title: “My application does all sorts of cool things”, 
      body: “<a href=’http://www.mysite.com’>Click here</a> for more information”,
      url: “http://www.mysite.com/”
   }
}).execute();

