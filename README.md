#JACE

Simple Web Data Service API in PHP built for web applications that require authentication over https. 

It features a built-in authentication layer and connection monitor. Returns data in standard JSON.

Verifies requests using data modeling templates. 

Supports default and named query models, multiple POST directives at each endpoint (i.e. endpoint: "/staff" - directives might include: "listStaff" or "addStaff" or "updateStaff")

Uses the Epiphany PHP Micro Framework for endpoint routing but you can use whatever you like.