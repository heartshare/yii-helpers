# Alias that simplify the generation of a new Yii app

# This function simply generate a new Yii app in the htdocs folder
function createYiiAppFnc(){
	cd /Applications/MAMP/yii_framework/framework; 
	php yiic webapp /Applications/MAMP/htdocs/$1;
	cd /Applications/MAMP/htdocs/$1;
}
alias createYiiApp=createYiiAppFnc
# This function generate a new Yii app in the htdocs folder and
# runs the grunt-init task
function createYiiAppFullFnc(){
	cd /Applications/MAMP/yii_framework/framework; 
	php yiic webapp /Applications/MAMP/htdocs/$1;
	cd /Applications/MAMP/htdocs/$1;
	grunt-init yii;
	npm install;
	mkdir -m 0777 protected/backend/runtime;
}
alias createYiiAppFull=createYiiAppFullFnc