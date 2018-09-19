#Running on any platform
--------------------
Copy the contents of the source directory (linux_src if you are running it on linux, windows_src if running on windows) into the web root for the apache server.
The two source distributions should be identical save for the file encoding (windows vs \*nix) and a variable called webroot, which I will get to in a moment.

This software was created in a docker development environment, so the make targets use docker version 18 to build and deploy a linux container with Apache 2 and PHP 7.2.
Using versions that are different from those two software versions may result in unknown behavior. These versions are the ones in the lab (I asked the IT staff).

#About the webroot variable
-------------------
there is a variable on line 7 of src/php/Acme.php that specifies the webroot of the server, This variable is used to 
 specify the webroot of the apache server so that the Acme server can request data from big stuff and little stuff via
 http using curl. If this web root is not correctly configured then Acme will be unable to communicate with big stuff
 and little stuff. If this is the windows distribution, it should be configured to utilize the apache server on the K drive
 in the lab at my username. My username in that variable should be changed to your username if you are using the Apache in the labs.

 If you are using another setup, please ask me for what the name of the variable should be.
