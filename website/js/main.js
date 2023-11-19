const libList = document.getElementById("libList");
const menuBtn = document.getElementById("menuBtn");
const searchBtn = document.getElementById("searchBtn");
const myLibMenu = document.getElementById("myLibMenu");
const mSearch = document.getElementById("mSearch");
const notifiBtn = document.getElementById("notifiBtn");
const chatSide = document.getElementById("chatSide");
const trans1 = document.getElementById("trans1");
const trans2 = document.getElementById("trans2");
const notifications = document.getElementById("notifications");
const notificationsDot=document.getElementById("notificationsDot");
let listenersExist;
let atMedia768exe;
const addLibIcon = document.getElementById("addLibIcon");
const addLibLayout = document.getElementById("addLibLayout");
const closeAddLibForm = document.getElementById("closeAddLibForm");
const addLibLayoutTrans = document.getElementById("addLibLayoutTrans");
const libTitle = document.getElementById("libTitle");
const createLibBtn = document.getElementById("createLibBtn");
const logout = document.getElementById("logout");
const storesLayout = document.getElementById("storesLayout");
const closeStoresForm = document.getElementById("closeStoresForm");
const storesList = document.getElementById("storesList");
const storesTrans = document.getElementById("storesTrans");
//post vars
const postFormLayout = document.getElementById("postFormLayout");
const postFormTrans = document.getElementById("postFormTrans");
const closePostForm = document.getElementById("closePostForm");
const postContent = document.getElementById("postContent");
const postContentTxt = document.getElementById("postContentTxt");
const postBtn = document.getElementById("postBtn");
const postForm_myLibs=document.getElementById("postForm_myLibs");
let postBookID = 0;
let lastPostBook;
//chat vars
const chat = document.getElementById("chat");
let chatBtn;
let lastChatMsg;
let preChatMsg;
const chatC = document.getElementById("chatC");
const chattedImg = document.getElementById("chattedImg");
const chattedTitle = document.getElementById("chattedTitle");
const chattedLib = document.getElementById("chattedLib");
const chatMsg = document.getElementById("chatMsg");
const sendChatMsgBtn = document.getElementById("sendChatMsgBtn");
const moreChat= document.getElementById("moreChat");
//search vars
const sSearchBtn=document.getElementById("sSearchBtn");
const sSearchKeyword= document.getElementById("sSearchKeyword");
const mSearchBtn=document.getElementById("mSearchBtn");
const mSearchKeyword= document.getElementById("mSearchKeyword");
//-----------
//when any page starts
docInit();
window.onresize = atMedia768;
notifiBtn.addEventListener("click", openNotificationsL);
notificationsDot.addEventListener("click", openNotificationsL);
notificationsSeen()

searchBtn.addEventListener("click", openSearch);
trans1.addEventListener("click", openMenu);
trans2.addEventListener("click", openChat);

addLibIcon.addEventListener("click", toggleAddLibLayout);
closeAddLibForm.addEventListener("click", toggleAddLibLayout);
addLibLayoutTrans.addEventListener("click", toggleAddLibLayout);
libTitle.addEventListener('keyup',(eve)=>{
	if(eve.code=='Enter'){
		addLib();
	}
})
createLibBtn.addEventListener("click", addLib);
logout.addEventListener("click", () => {
	post("logout.php", {}, (res) => {
		res = JSON.parse(res);
		if (res) {
			window.open("./", "_self");
		}
	});
});
closeStoresForm.addEventListener("click", () => toggleStores());
storesTrans.addEventListener("click", () => toggleStores());
closePostForm.addEventListener("click", () => togglePost());
postFormTrans.addEventListener("click", () => togglePost());
if (currentLibID !== "undefined") {
}
//to hide component when user clicked on other area
document.addEventListener("click",(eve)=>{
	if(eve.target != notifications && eve.target.parentElement!=notifications && eve.target!=notifiBtn && eve.target!=notificationsDot && !notifications.classList.contains("display")){
		openNotifications();
	}
})
//chat
sendChatMsgBtn.addEventListener("click", sendChatMsg);

let keysdown = [];
let keysCombination = "";
chatMsg.addEventListener("keydown", (eve) => {
	if (!keysdown.includes(eve.code)) {
		keysdown.push(eve.code);
		if (keysdown.length == 1) {
			keysCombination = keysdown[0];
		} else {
			keysCombination = keysdown.join("+");
		}
	}
	if(keysCombination.includes('Enter') && !keysCombination.includes('ShiftRight') ){
		setTimeout(()=>{
			if(chatMsg.innerText.startsWith('\n') && keysCombination.includes('Enter')){
				chatMsg.innerText=''
			}
		},1);
		sendChatMsg();
	}
});
chatMsg.addEventListener("keyup", (eve) => {
	if (keysdown.includes(eve.code)) {
		keysdown.splice(keysdown.indexOf(eve.code), 1);
	}
});
chatC.addEventListener('scroll',chatEnd);
//search
sSearchBtn.addEventListener('click',sSearchLib);
mSearchBtn.addEventListener('click',mSearchLib);
document.body.addEventListener("keydown", function (event) {
	if (event.code === "Enter") {
		if (Array(sSearchKeyword).includes(document.activeElement)) {
			sSearchLib();
		}else if(Array(mSearchKeyword).includes(document.activeElement)){
			mSearchLib();
		}
	}
});
//end when any page starts
//for the root page
postBtn.addEventListener("click", () => {
	let bookID = postBookID;
	post(
		"processes.php",
		{
			process: 12,
			content: postContentTxt.innerText,
			lib_id: postForm_myLibs.value,
			book_id: bookID,
		},
		() => {
			postBookID = 0;
			togglePost();
			getLastPosts();
		}
	);
});
//for lib page
initReadBtn();
