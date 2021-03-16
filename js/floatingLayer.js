var y1 = 20;   // change the # on the left to adjuct the Y co-ordinate
(document.getElementById) ? dom = true : dom = false;

function hideIt() {
  if (dom) {document.getElementById("layer1").style.visibility='hidden';}
  if (document.layers) {document.layers["layer1"].visibility='hide';} }

function showIt() {
  if (dom) {document.getElementById("layer1").style.visibility='visible';}
  if (document.layers) {document.layers["layer1"].visibility='show';} }

function placeIt() {
  if (dom && !document.all) {document.getElementById("layer1").style.top = window.pageYOffset + (window.innerHeight - (window.innerHeight-y1))}
  if (document.layers) {document.layers["layer1"].top = window.pageYOffset + (window.innerHeight - (window.innerHeight-y1))}
  if (document.all) {document.all["layer1"].style.top = document.body.scrollTop + (document.body.clientHeight - (document.body.clientHeight-y1));}
  window.setTimeout("placeIt()", 10); }

window.onload=placeIt;
onResize="window.location.href = window.location.href"
