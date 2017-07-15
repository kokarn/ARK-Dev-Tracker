# Project moved

# Please go to https://github.com/post-tracker/site for the current development.



# ARK-Dev-Tracker
Dev Tracker for ARK: Survival Evolved

[![devDependency Status](https://david-dm.org/kokarn/ark-dev-tracker/dev-status.svg)](https://david-dm.org/kokarn/ark-dev-tracker#info=devDependencies)

## Feedback
[ARK Dev Tracker on Reddit](https://www.reddit.com/r/playark/comments/3blzar/ark_dev_tracker/)

## To run a mirror or something
* Clone repository
```git clone https://github.com/kokarn/ARK-Dev-Tracker.git```
* Run ```actions/setup.php``` from the web to add all the current devs
* Set something like cron to run ```actions/update.php``` as often as you like
* Done. It should now be accessible if you just browse to the folder where index.html is

## For development
```shell
git clone https://github.com/kokarn/ARK-Dev-Tracker.git
cd ARK-Dev-Tracker
npm install
grunt watch
```
