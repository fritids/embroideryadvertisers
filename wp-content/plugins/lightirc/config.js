var params = {};
params.host                         = "irc.mach7enterprises.com";
params.port                         = 6667;
params.policyPort                   = 9024;
params.language                     = "en";
params.styleURL                     = "css/black.css";
params.nick                         = "lightIRC_%";
params.autojoin                     = "#test";
params.perform                      = "";
params.showServerWindow             = true;
params.showNickSelection            = true;
params.showIdentifySelection        = false;
params.showRegisterNicknameButton   = false;
params.showRegisterChannelButton    = false;
params.showNewQueriesInBackground   = false;
params.navigationPosition           = "bottom";
function sendCommand(command) {
  swfobject.getObjectById('lightIRC').sendCommand(command);
}
function sendMessageToActiveWindow(message) {
  swfobject.getObjectById('lightIRC').sendMessageToActiveWindow(message);
}
function setTextInputContent(content) {
  swfobject.getObjectById('lightIRC').setTextInputContent(content);
}
function onChatAreaClick(nick, ident, realname) {
  //alert("onChatAreaClick: "+nick);
}
function onContextMenuSelect(type, nick, ident, realname) {
  alert("onContextMenuSelect: "+nick+" for type "+type);
}
function onServerCommand(command) {
  return command;
}
window.onbeforeunload = function() {
  swfobject.getObjectById('lightIRC').sendQuit();
}
for(var key in params) {
  params[key] = params[key].toString().replace(/%/g, "%25");
}