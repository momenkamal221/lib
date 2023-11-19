function post(url, data, success) {
	let init = {};
	init.method = "POST";
	init.body= Object.entries(data).map(([k,v])=>{return k+'='+v}).join('&');
	init.headers ={ 'Content-Type': 'application/x-www-form-urlencoded',};

	async function postPromise() {
		try {
			let res = await fetch(url, init);
			return await res.text();
		} catch (error) {
			console.log(error);
		}
	}
	if (typeof success === "function"){
		postPromise()
		.then(
			(res) => {
			success(res);
		}
		);
	}
}
function addLib(){
	if(!libTitle.value){
		libTitle.placeholder='Library title can not be empty';
		libTitle.classList.add('err-PH');
		return;
	}
	post(
		"processes.php",
		{
			process: 0,
			lib_title: libTitle.value,
		},
		(res) => {
			res = JSON.parse(res);
			window.open(`library.php?id=${res.lib_id}`, "_self");
		}
	);
}
function openMenu(x) {
	menuBtn.classList.toggle("change");
	if (!libList.classList.contains("display")) {
		libList.classList.toggle("hide-list");
		setTimeout(() => libList.classList.toggle("display"), 300);
	} else {
		libList.classList.toggle("display");
		setTimeout(() => {
			handleListHeight();
			libList.classList.toggle("hide-list");
		}, 1);
	}
	click(menuBtn);
	toggleCheck(x, menuBtn);
}
function openChat(x) {
	if (window.innerWidth > 768) {
		if (chatSide.classList.contains("display")) {
			chatSide.classList.toggle("display");
			setTimeout(() => {
				handleListHeight();
				chatSide.classList.toggle("hide-chat");
			}, 1);
		}
	} else {
		if (!chatSide.classList.contains("display")) {
			chatSide.classList.toggle("hide-chat");
			setTimeout(() => chatSide.classList.toggle("display"), 300);
		} else {
			chatSide.classList.toggle("display");
			setTimeout(() => {
				handleListHeight();
				chatSide.classList.toggle("hide-chat");
			}, 1);
		}
		toggleCheck(x, chatBtn);
	}
}
function openSearch() {
	click(searchBtn);
	mSearch.classList.toggle("display");
	if(!mSearch.classList.contains("display")){
		mSearchKeyword.focus();
	}
}
function openNotifications(x) {
	click(notifiBtn);
	if (window.innerWidth < 768) {
		toggleCheck(x, notifiBtn);
	}
	notifications.classList.toggle("display");
}

function click(x) {
	x.classList.toggle("_clicked");
}
function toggleCheck(x, y) {
	if (x) {
		toggleAll(y);
	}
}
function toggleAll(y) {
	if (y !== menuBtn && menuBtn.classList.contains("_clicked")) openMenu();
	if (y !== notifiBtn && notifiBtn.classList.contains("_clicked"))
		openNotifications();
	if (y !== chatBtn && !chatSide.classList.contains("display")) openChat();
}

function handleListHeight() {
	const list = document.getElementById("list");
	list.style.height = `${window.innerHeight - 260}px`;
	chat.style.height = `${window.innerHeight - 320}px`;
	document.getElementsByClassName('chat-frame')[0].style.height = `${window.innerHeight}px`;
}
function atMedia768() {
	if (atMedia768exe) return;
	atMedia768exe = true;
	if (window.innerWidth > 768) {
		if (listenersExist) {
			removeListeners();
		}
		if (libList.classList.contains("hide-list")) {
			openMenu();
		}
		if (chatSide.classList.contains("display")) {
			openChat();
		}
		if (!mSearch.classList.contains("display")) {
			openSearch();
		}
	} else {
		if (!listenersExist) {
			addListeners();
		}
		if (!libList.classList.contains("hide-list")) {
			openMenu();
		}
		if (!chatSide.classList.contains("hide-chat")) {
			openChat();
		}
	}
	handleListHeight();
	atMedia768exe = false;
}
function openMenuL() {
	openMenu(menuBtn);
}
function openNotificationsL() {
	openNotifications(notifiBtn);
	post('processes.php',
	{
		process:22
	}
	,()=>notificationsSeen()
	
	)
}
function removeListeners() {
	menuBtn.removeEventListener("click", openMenuL);
	listenersExist = false;
}
function addListeners() {
	menuBtn.addEventListener("click", openMenuL);
	listenersExist = true;
}
function docInit() {
	if (window.innerWidth > 768) {
		removeListeners();
		openMenu();
		openChat();
	} else {
		addListeners();
	}
}
function toggleAddLibLayout() {
	addLibLayout.classList.toggle("display");
}
function initReadBtn() {
	const readBtn = document.getElementById("readBtn");
	if (readBtn) {
		readBtn.addEventListener("click", () => {
			post(
				"processes.php",
				{
					process: 13,
					lib_id: currentLibID,
				},
				(res) => {
					res = JSON.parse(res);
					if (res.state === 1) {
						let readBtn2HTML = `<span class="read-btn" id="readBtn">
							Read
						</span>`;
						readBtn2HTML = new DOMParser().parseFromString(
							readBtn2HTML,
							"text/html"
						).body.firstChild;
						readBtn.replaceWith(readBtn2HTML);
					} else if (res.state === 2) {
						let readBtn2HTML = `<span class="reading-btn" id="readBtn">
							Reading...
						</span>`;
						readBtn2HTML = new DOMParser().parseFromString(
							readBtn2HTML,
							"text/html"
						).body.firstChild;
						readBtn.replaceWith(readBtn2HTML);
					}
					initReadBtn();
				}
			);
		});
	}
}
function bsScroll(bsID, rArrowID, lArrowID) {
	const bs = document.getElementById(bsID);
	document.getElementById(lArrowID).addEventListener("click", () => {
		bs.offsetWidth;
		bs.scrollBy(-bs.offsetWidth, 0);
	});
	document.getElementById(rArrowID).addEventListener("click", () => {
		bs.offsetWidth;
		bs.scrollBy(bs.offsetWidth, 0);
	});
}
function reqAddToList(bsID, bookID, btnID) {
	const req = () => {
		post(
			"processes.php",
			{
				process: 7,
				book_id: bookID,
				list_id: bsID,
			},
			() => {
				location.reload();
			}
		);
	};
	document.getElementById(btnID).addEventListener("click", req);
}
function getStores(bookID) {
	if(!bookID){
		bookID=0;
	}
	post(
		`processes.php`,
		{
			process: 2,
			book_id: bookID,
		},
		(res) => {
			if(res==='no book') return;
			res = JSON.parse(res);
			let stores = res;
			if (res) {
				for (let store of stores) {
					const a = document.createElement("a");
					const li = document.createElement("li");
					a.href = store["link"];
					a.target = "_blank";
					a.innerText = store["store"];
					li.appendChild(a);
					storesList.appendChild(li);
				}
			} else {
				storesList.innerHTML = `<div style="color:tomato">no stores</div>`;
			}
			toggleStores();
		}
	);
}
function toggleStores() {
	storesLayout.classList.toggle("display");
	//remove store list childs
	if (storesLayout.classList.contains("display")) {
		var child = storesList.lastElementChild;
		while (child) {
			storesList.removeChild(child);
			child = storesList.lastElementChild;
		}
	}
}
function togglePost(book,bookID) {
	if(bookID){
		postBookID = bookID;
	}
	postFormLayout.classList.toggle("display");
	if (book) {
		lastPostBook=document.getElementById('lastPostBook');
		if(lastPostBook){
			lastPostBook.remove();
		}
		let bookHTML = `
		<div class="post-form__book" id="lastPostBook">
			<img src="${book}" alt="" class="post-form__book-cover">
		</div>`;
		bookHTML = new DOMParser().parseFromString(bookHTML, "text/html").body
			.firstChild;
		postContent.appendChild(bookHTML);
	}
	postContentTxt.innerText='';
	postContentTxt.focus();
	
}
function reqRmBook(rmID) {
	rmID = rmID.split("-");
	let bookID = rmID[1];
	bookID = bookID.substring(1);
	let bsID = rmID[2];
	bsID = bsID.substring(2);
	post(
		"processes.php",
		{
			process: 8,
			book_id: bookID,
			list_id: bsID,
		},
		() => {
			location.reload();
		}
	);
}
function reqRmBs(rmBsBtnID) {
	const bsID = rmBsBtnID.split("-")[1];
	post(
		"processes.php",
		{
			process: 9,
			list_id: bsID,
			lib_id:currentLibID
		},
		() => {
			location.reload();
		}
	);
}
function get(name) {
	if (
		(name = new RegExp("[?&]" + encodeURIComponent(name) + "=([^&]*)").exec(
			location.search
		))
	)
		return decodeURIComponent(name[1]);
}
//chat functions

function notification_openChat(libID) {
	openChat();
	chattedLibID=libID;
	createChat();
	openNotifications();
	return false;
}
function openChatL(eve) {
	chatBtn=eve.currentTarget;
	openChat(chatBtn);
	chattedLibID=chatBtn.id.split('-')[1];
	createChat();
}

function createChat() {
	lastChatMsg=0
	preChatMsg=0;
	post(
		'processes.php',
		{
			process:16,
			lib_id:chattedLibID
		},(res)=>{
			chat.style.display='';
			chattedLib.style.display='';
			chatMsg.focus();
			if(res)
			res=JSON.parse(res);
			chatC.innerHTML='';
			chatC.appendChild(moreChat);
			chattedImg.src=res.libPic;
			chattedTitle.innerText=res.libTitle;
			chattedLib.href=`library.php?id=${chattedLibID}` ;
			connectionHundler();
		}
	);
}
let requestingMsgs=false;
function connectionHundler(){
	if(requestingMsgs) return;
	requestingMsgs=true;
	setTimeout(() => {
		getLastMsgs();
	}, 250);
}

function sendChatMsg(){
	chatMsgText=chatMsg.innerText;
	while(chatMsgText.startsWith('\n')){
		chatMsgText=chatMsgText.replace('\n','');
	}
	while(chatMsgText.endsWith('\n')){
		chatMsgText=chatMsgText.slice(0,-1);
	}
	if(chatMsgText=='')return;
	else if(chatMsgText.startsWith('\n')){
		return;
	}
	post(
		'processes.php',
		{
			process:17,
			chatted_lib_id:chattedLibID,
			msg:chatMsgText
		},
		(res)=>{
			chatMsg.innerText='';
			chatMsgText='';
			connectionHundler();
		}
	);
}
function getLastMsgs(){
	if(!lastChatMsg){
		lastChatMsg=0;
	}

	post(
		'processes.php',
		{
			process:18,
			task:1,
			lib_id:chattedLibID,
			last_msg:lastChatMsg

		},(res)=>{
			let msgs=JSON.parse(res);
			if(msgs){
				for(let msg of msgs){
					if(msg.to_lib_id==chattedLibID){
						createChatRow(1,msg._message,msg._date,true)
					}else{
						createChatRow(2,msg._message,msg._date,true)
					}
					lastChatMsg=msg.msg_id;
				}
				if(preChatMsg===0){
					preChatMsg=msgs[0].msg_id;
				}
			}
			requestingMsgs=false;
			connectionHundler();
		}
	)
}
function getPreMsgs(){
	post(
		'processes.php',
		{
			process:18,
			task:2,
			lib_id:chattedLibID,
			pre_msg:preChatMsg
		},
		(res)=>{
			let msgs=JSON.parse(res);
			moreChat.remove();
			if(msgs){
				for(let msg of msgs){
					if(msg.to_lib_id==chattedLibID){
						createChatRow(1,msg._message,msg._date,false);
					}else{
						createChatRow(2,msg._message,msg._date,false);
					}
				}
				preChatMsg=msgs[msgs.length-1].msg_id;
			}
			chatC.appendChild(moreChat);
			chatC.addEventListener('scroll',chatEnd);
		}
	)
}
function createChatRow(user,msg,date,last){
	date=new Date(date*1000);
	hours= String(date.getHours()).padStart(2, '0');
	minutes= String(date.getMinutes()).padStart(2, '0');
	day= String(date.getDay()).padStart(2, '0');
	dayName=date.toLocaleString('default', { weekday: 'short' });
	month= date.toLocaleString('default', { month: 'short' });
	year= String(date.getYear()).padStart(2, '0');
	if(Date.now()-date.getTime()>511200000){
		date=`${month} ${day}, ${year}, ${hours}:${minutes}`
	}else{
		date=`${dayName} ${hours}:${minutes}`
	}
	rowHTML=`
	<div class=".chat__row chat__u${user}">
		<div class="chat__msg-c">
			<div class="chat__msg">${msg}</div>
			<div class="chat__time">${date}</div>
		</div>
	</div>`
	rowHTML = new DOMParser().parseFromString(rowHTML, "text/html").body
	.firstChild;
	if(last){
		chatC.prepend(rowHTML);
	}else{
		chatC.appendChild(rowHTML);
	}
}
function chatEnd(){
	if (chatC.getBoundingClientRect().top < moreChat.getBoundingClientRect().top) {
		chatC.removeEventListener('scroll',chatEnd);
		getPreMsgs();
	}
}
function sSearchLib(){
	searchLib(sSearchKeyword.value);
}
function mSearchLib(){
	searchLib(mSearchKeyword.value);
}
function searchLib(keyword){
	if(keyword===''){
		return;
	}
	while(keyword.search(' ')!=-1){
		keyword=keyword.replace(' ','%');
	}
	window.open(`search.php?q=${keyword}`,'_self')
}
function notificationsSeen(){
	post('processes.php',
	{
		process:21
	},(seen)=>{
		seen=JSON.parse(seen);
		if(seen){
			notificationsDot.classList.add('display');
		}else{
			notificationsDot.classList.remove('display');
			getLastNotifcations();
		}
		notificationsSeenCheck()
	}
	)
}
function notificationsSeenCheck(){
	setTimeout(()=>{
		notificationsSeen();
	},5000);
}
function getLastNotifcations(){
	const lastNotifcation=notifications.firstElementChild;
	const lastNotifcationID=lastNotifcation.id.split('-')[1];
	post('processes.php',{process:23,last_noti:lastNotifcationID},
	(res)=> {
		console.log(res)
		notifications.insertAdjacentHTML('afterbegin',res);
	}
	)
}

//start
let currentLibID;
let chattedLibID;
let chatMsgText;