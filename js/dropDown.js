var initHeight = 0;
var slidedown_direction = 1;
var slidedownContentBox = false;
var slidedownContent = false;
var slidedownActive = false;
var contentHeight = false;
var slidedownSpeed = 3;  // Higher value = faster script
var slidedownTimer = 7; // Lower value = faster script

function slidedown_showHide() {
  if(initHeight==0)slidedown_direction=slidedownSpeed; else slidedown_direction = slidedownSpeed*-1;
  if(!slidedownContentBox) {
    slidedownContentBox = document.getElementById('dhtmlgoodies_contentBox');
    slidedownContent = document.getElementById('dhtmlgoodies_content');
    contentHeight = document.getElementById('dhtmlgoodies_content').offsetHeight;
  }
  slidedownContentBox.style.visibility='visible';
  slidedownActive = true;
  slidedown_showHide_start();
}

function slidedown_showHide_start() {
  if(!slidedownActive)return;
  initHeight = initHeight/1 + slidedown_direction;
  if(initHeight <= 0) {
    slidedownActive = false; 
    slidedownContentBox.style.visibility='hidden';
    initHeight = 0;
  }
  if(initHeight>contentHeight) {
    slidedownActive = false; 
  }
  slidedownContentBox.style.height = initHeight + 'px';
  slidedownContent.style.top = initHeight - contentHeight + 'px';
  setTimeout('slidedown_showHide_start()',slidedownTimer); // Choose a lower value than 10 to make the script move faster
}

function setslidedownWidth(newWidth) {
  document.getElementById('dhtmlgoodies_slidedown').style.width = newWidth + 'px';
  document.getElementById('dhtmlgoodies_contentBox').style.width = newWidth + 'px';
}

function setSlideDownSpeed(newSpeed) {
  slidedownSpeed = newSpeed;
}

