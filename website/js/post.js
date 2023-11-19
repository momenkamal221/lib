const morePosts = document.getElementById("morePosts");
document.addEventListener("scroll", pageEnd);
function pageEnd() {
	if(morePosts)
	if (morePosts.getBoundingClientRect().top < window.innerHeight) {
		document.removeEventListener("scroll", pageEnd);
		getPrePosts();
	}
}
function getLastPosts() {
	const lastPost = postContainer.firstElementChild;
	let lastPostID = 0;
	if (lastPost) {
		lastPostID = lastPost.id;
		lastPostID = lastPostID.split("-")[1];
	}
	post(
		"processes.php",
		{
			process: 5,
			last_post_id: lastPostID,
			lib_id: currentLibID
		},
		(res) => {
			let posts = JSON.parse(res);
			if (posts) {
				for (let post of posts) {
					createPost(
						post.post_id,
						post.lib_id,
						post.book_id,
						post.lib_pic,
						post.content,
						post.likes_count,
						post.liked,
						post.lib_title,
						post._date,
						true
					);
				}
			}
		}
	);
}
function getPrePosts() {
	const prePost = postContainer.lastElementChild;
	if (prePost) {
		let prePostID = prePost.id;
		prePostID = prePostID.split("-")[1];
		post(
			"processes.php",
			{
				process: 5,
				pre_post_id: prePostID,
				lib_id: currentLibID
			},
			(res) => {
				let posts = JSON.parse(res);
				if (posts) {
					for (let post of posts) {
						createPost(
							post.post_id,
							post.lib_id,
							post.book_id,
							post.lib_pic,
							post.content,
							post.likes_count,
							post.liked,
							post.lib_title,
							post._date,
							false
						);
					}
				}
				document.addEventListener("scroll", pageEnd);
			}
		);
	}
}
function toggleLike(postID, likeBtn, postLikesID) {
	post(
		`processes.php`,
		{
			process: 1,
			post_id: postID,
		},
		(res) => {
			console.log(res)
			res = JSON.parse(res);
			const postLikes = document.getElementById(postLikesID);
			postLikes.innerText = res.likes_count;
			if (res.liked) {
				likeBtn.classList.add("_post-btn-clicked");
			} else if (!res.liked) {
				likeBtn.classList.remove("_post-btn-clicked");
			}
		}
	);
}

function createPost(
	postID,
	libID,
	bookID,
	libPic,
	content,
	likes,
	liked,
	libTitle,
	date,
	first
) {
	if (liked) {
		liked = "_post-btn-clicked";
	} else {
		liked = "";
	}
	date = new Date(date*1000).toDateString();
	
	let postHTML = `
	<div class="post" id="post-${postID}">
	
	<a href="library.php?id=${libID}">
		<div class="post__lib">
			<img class="post__lib-img" src="data/uploads/lib-pics/${libPic}" alt="${libTitle}" />
			<div class="post__name">${libTitle}</div>
		</div>
	</a>
	<div class="post__content-slab" id="content-slab-post-${postID}">
		<pre><div class="post__content" id="content-post-${postID}">${content}</div></pre>
	</div>
	<div class="post__bar"><span class="post__likes" id="post-likes-${postID}">${likes}</span> Likes <a href="./post.php?post=${postID}" class="post__date">${date}</a></div>
	<div class="post__menu">
		<div class="post__menu-icon ${liked}" id="post-like-btn-${postID}">
			<img src="website/images/like.png" alt="" />
		</div>
		<div class="post__menu-icon"id="post-comment-btn-${postID}">
			<img src="website/images/comment.png" alt="" />
		</div>
		<div class="post__menu-icon" id="post-stores-btn-${postID}">
			<img src="website/images/buy.png" alt="" />
		</div>
	</div>
	<div class="comments-slab display" id="comment-slab-${postID}">
		<div class="add-comment-c">
			<textarea type="text" class="intxt" id="add-comment-${postID}"></textarea>
			<div class="add-comment-btn" id="add-comment-btn-${postID}">Comment</div>
		</div>
		<div class="comments" id="post-comments-${postID}">
		</div>
		<div class="view-more-comments" id="more-comments-${postID}">...More comments...</div>
	</div>
	`;

	postHTML = new DOMParser().parseFromString(postHTML, "text/html").body
		.firstChild;
	if(first){
		postContainer.prepend(postHTML);
	}else{
		postContainer.appendChild(postHTML);
	}

	const likeBtn = document.getElementById(`post-like-btn-${postID}`);
	likeBtn.addEventListener("click", () =>
		toggleLike(postID, likeBtn, `post-likes-${postID}`)
	);
	const storesBtn = document.getElementById(`post-stores-btn-${postID}`);
	storesBtn.addEventListener("click", () => getStores(bookID));

	//Comments
	const commentSlab = document.getElementById(`comment-slab-${postID}`)
	const postComments = document.getElementById(`post-comments-${postID}`); //the container of the comments
	const commentBtn = document.getElementById(`post-comment-btn-${postID}`);
	commentBtn.addEventListener("click", () => {
		getLastComments(postID, postComments,commentSlab);
		document
			.getElementById(`comment-slab-${postID}`)
			.classList.toggle("display");
	});

	const viewMoreCommentsBtn = document.getElementById(
		`more-comments-${postID}`
	);
	viewMoreCommentsBtn.addEventListener("click", () =>
		getPreComments(postID, postComments,commentSlab)
	);
	if(bookID){
		post('processes.php',
		{
			process:14,
			book_id:bookID
		},
		(res)=>{
			res= JSON.parse(res);
			let bookHTML=`
			<div class="book-c posted-book">
				<img src="data/uploads/book-covers/${res.cover}" alt="" />
				<div class="book-menu-btn">
					<img src="website/images/three-dots.svg" alt="" class="three-dots">
					<ol class="book-menu">
						<li id="postbook-${postID}-${bookID}">post</li>
						${readBook(res.book_location,bookID)}
						<li id="postbookstores-${postID}-${bookID}">Stores</li>
						<li><a href="book.php?id=${bookID}">info</a></li>	
					</ol>
				</div>
			</div>`;
			bookHTML=new DOMParser().parseFromString(bookHTML, "text/html").body.firstChild;
			document.getElementById(`content-slab-post-${postID}`).prepend(bookHTML);
			const postBookBtn= document.getElementById(`postbook-${postID}-${bookID}`);
			postBookBtn.addEventListener('click',()=>togglePost(`data/uploads/book-covers/${res.cover}`,bookID));
			const postBookStores= document.getElementById(`postbookstores-${postID}-${bookID}`);
			postBookStores.addEventListener('click',()=>getStores(bookID));
		}
		)
	}
	const addCommentBtn= document.getElementById(`add-comment-btn-${postID}`);
	addCommentBtn.addEventListener("click", ()=>{
		const comment =document.getElementById(`add-comment-${postID}`);
		if(comment.value==''){
			return;
		}
		post(
			'processes.php',
			{
				process:15,
				post_id: postID,
				comment:comment.value
			},
			(res)=>{
				getLastComments(postID, postComments,commentSlab);
				comment.value='';
			}
		)
	})
}
function readBook(book,bookID){
	if(book){
		return `<li><a href="read.php?bookid=${bookID}">read</a></li>`;
	}
	return '';
}

function createComment(
	postID,
	libID,
	libTitle,
	lib_pic,
	postComments,
	commentID,
	comment,
	last,
	date
) {
	date = new Date(date*1000).toDateString();
	let commentHTML = `
	<div class="comment-c" id="${postID}-${commentID}">
		<a href="library.php?id=${libID}">
			<div class="comment__lib">
				<img src="data/uploads/lib-pics/${lib_pic}" class="comment__lib-pic" />
				<div class="comment__lib-name">${libTitle}</div>
			</div>
		</a>
		<div class="comment"><pre>${comment}</pre>
		</div>
		<div class="comment__date">${date}</div>
	</div>
	`;
	commentHTML = new DOMParser().parseFromString(commentHTML, "text/html").body
		.firstChild;
	if (last) {
		postComments.appendChild(commentHTML);
	} else {
		postComments.prepend(commentHTML);
	}
}

function getLastComments(postID, postComments,commentSlab) {
	const noMoreComments = postComments.lastElementChild;
	if (noMoreComments) {
		if (noMoreComments.classList.contains("no-more-comments")) {
			return;
		}
	}
	const lastComment = postComments.firstElementChild;
	//incase there is no comments
	let lastCommentID = 0;
	if (lastComment) {
		lastCommentID = lastComment.id.split("-")[1];
	}
		post(
			"processes.php",
			{
				process: 3,
				post_id: postID,
				last_comment_id: lastCommentID,
			},
			(res) => {
				let comments = JSON.parse(res);

				if (comments){
					for (let comment of comments) {
						createComment(
							postID,
							comment.lib_id,
							comment.lib_title,
							comment.lib_pic,
							postComments,
							comment.comment_id,
							comment.comment,
							false,
							comment._date
						);
					}
				}else{
					const viewMoreCommentsBtn = document.getElementById(
						`more-comments-${postID}`
					);
						const commentExist=postComments.lastElementChild;
					if (viewMoreCommentsBtn&& !commentExist) viewMoreCommentsBtn.remove();
					displayNoMoreComments(commentSlab);
				}
				
				
					
			}
		);
}
function displayNoMoreComments(commentSlab) {
	let noMore = document.createElement("div");
	noMore.innerText = "No more comments";
	noMore.classList.add("no-more-comments");
	if(commentSlab.lastElementChild.innerHTML!=noMore.innerHTML)
	commentSlab.appendChild(noMore);
}
function getPreComments(postID, postComments,commentSlab) {
	const viewMoreCommentsBtn = document.getElementById(
		`more-comments-${postID}`
	);
	const preComment = postComments.lastElementChild;
	let preCommentID;
	if (preComment) {
		preCommentID = preComment.id.split("-")[1];

		post(
			"processes.php",
			{
				process: 4,
				post_id: postID,
				pre_comment_id: preCommentID,
			},
			(res) => {
				let comments = JSON.parse(res);
				if(comments){
					for (let comment of comments) {
						createComment(
							postID,
							comment.lib_id,
							comment.lib_title,
							comment.lib_pic,
							postComments,
							comment.comment_id,
							comment.comment,
							true,
							comment._date
						);
				}
				}else {
					viewMoreCommentsBtn.remove();
					displayNoMoreComments(commentSlab);
				}
			}
		);
	} 
}

//-------------------------------------
