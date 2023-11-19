<?php
$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "lib";

$conn = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);
if (!$conn) {
	die("connection failed " . mysqli_connect_error());
}
function emailExists($conn, $email)
{
	$sql = "SELECT * FROM users WHERE email = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_bind_param($stmt, "s", $email);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	if ($row = mysqli_fetch_assoc($resultData)) {
		//code
	} else {
		return false;
	}
	mysqli_stmt_execute($stmt);
	return true;
}
function cleanInput($input)
{
	$input=trim($input);
	$input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
	$input = strip_tags($input);
	$input = stripslashes($input);
	return $input;
}
function login($email, $pwd, $conn)
{
	$userData = fetch($conn, 'SELECT * FROM users where email = "' . $email . '";');

	if ($userData && $pwd == $userData[0]['password']) {
		session_start();
		$_SESSION['user_id'] = $userData[0]['user_id'];
		$_SESSION['main_lib_id'] = fetch($conn, 'SELECT * FROM libraries where user_id = "' . $_SESSION['user_id'] . ' AND main = 1";')[0]['lib_id'];
		session_save_path();
		return true;
	}
	return false;
}
function isloggedin()
{
	if (isset($_SESSION['user_id'])) {
		return true;
	}
	return false;
}
function logout()
{
	session_unset();
	session_destroy();
}
function fetch($conn, $sql)
{
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	//make query & get result
	$result = mysqli_query($conn, $sql);
	//fetch the resulting rows as an array
	$data = array();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($data, $row);
	}
	$rowNum = mysqli_num_rows($result);
	if ($rowNum > 0) {
		return $data;
	}
	return false;
}
function haveLibPic($conn)
{
	$data = fetch($conn, 'SELECT lib_pic FROM libraries WHERE user_id = ' . $_SESSION['user_id'] . ' AND main = 1;');
	if ($data[0]['lib_pic'] == 0) {
		return false;
	}
	return true;
}

function getLibTitle($conn, $libID)
{
	return fetch($conn, 'SELECT lib_title FROM libraries WHERE lib_id = ' . $libID . ';')[0]['lib_title'];
}
function getLib($conn, $libID)
{
	return fetch($conn, 'SELECT * FROM libraries WHERE lib_id = ' . $libID . ';')[0];
}
function getMainLib()
{
	return $_SESSION['main_lib_id'];
}
function getMainLibOf($conn, $libID)
{
	$sql = 'SELECT lib_id FROM libraries WHERE user_id IN (SELECT user_id FROM libraries WHERE lib_id = ' . $libID . ') AND main = 1;';
	return fetch($conn, $sql)[0]['lib_id'];
}
function runQuery($conn, $sql)
{
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		//if user put code
		exit();
	}
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}
function own($conn, $libID)
{
	$userLibs = fetch($conn, 'SELECT lib_id FROM libraries WHERE user_id = ' . $_SESSION['user_id'] . ';');
	if ($userLibs) {
		foreach ($userLibs as $lib) {
			if ($lib['lib_id'] == $libID)
				return true;
		}
	}
	return false;
}
function ownBook($conn, $bookID)
{
	$book = getBook($conn, $bookID);
	$libID = $book['lib_id'];
	if (own($conn, $libID)) {
		return true;
	}
	return false;
}
function getLibPic($conn, $libID)
{
	return fetch($conn, 'SELECT lib_pic FROM libraries WHERE lib_id = ' . $libID . ';')[0]['lib_pic'];
}
function getLibCover($conn, $libID)
{
	return fetch($conn, 'SELECT lib_cover FROM libraries WHERE lib_id = ' . $libID . ';')[0]['lib_cover'];
}
function getListedBooks($conn, $listID)
{
	return fetch($conn, 'SELECT * FROM books WHERE book_id IN(SELECT book_id FROM listed_books WHERE list_id = ' . $listID . ');');
}
function getAllBooks($conn, $libID, $data)
{
	return fetch($conn, 'SELECT ' . $data . ' FROM books WHERE lib_id = ' . $libID . ';');
}
function getLists($conn, $libID)
{
	return fetch($conn, 'SELECT list_id,title FROM book_lists WHERE lib_id = ' . $libID . ';');
}
function getBook($conn, $bookID)
{
	return fetch($conn, 'SELECT * FROM books WHERE book_id = ' . $bookID . ';')[0];
}
function getStores($conn, $bookID)
{
	return fetch($conn, 'SELECT * FROM stores WHERE book_id = ' . $bookID . ';');
}
function read($conn, $libID)
{
	return fetch($conn, 'SELECT * FROM readers WHERE reader = ' . $_SESSION['main_lib_id'] . ' AND reading = ' . $libID . '  ;');
}
function libRead($conn, $lib1ID, $lib2ID)
{
	return fetch($conn, 'SELECT * FROM readers WHERE reader = ' . $lib1ID . ' AND reading = ' . $lib2ID . '  ;');
}
function notifyUser($conn, $libID, $action, $toLibID, $commentID, $postID, $bookID)
{
	$actions = ['post', 'comment', 'chat', 'book', 'msg_send','like_post'];
	if (!in_array($action, $actions)) {
		return false;
	}
	if (in_array($action, ['book', 'post']) && $toLibID !== 'all') {
		return false;
	}
	$date = time();
	if ($toLibID == 'all') {
		$readingLibs = fetch($conn, 'SELECT reader FROM readers WHERE reading = ' . $libID . ' AND reader != reading ;');
		if (!$readingLibs) exit();
		foreach ($readingLibs as $reader) {
			$toLibID = $reader['reader'];
			//dont notfy for a lib owned by user
			if (own($conn, $toLibID)) continue;
			$sql =
				'INSERT INTO notifications(lib_id,_action,to_lib_id,comment_id,post_id,book_id,_date) VALUES (?,?,?,?,?,?,?);';
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				//if user put code
				exit();
			}
			mysqli_stmt_bind_param($stmt, "isiiiii", $libID, $action, $toLibID, $commentID, $postID, $bookID, $date);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$sql =
				'INSERT INTO unseen_notifications(lib_id) VALUES (?);';
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)) {
				//if user put code
				exit();
			}
			mysqli_stmt_bind_param($stmt, "i", $toLibID);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		}
	} else {
		//dont notfy for a lib owned by user
		if (own($conn, $toLibID)) return false;
		$sql =
		'INSERT INTO notifications(lib_id,_action,to_lib_id,comment_id,post_id,book_id,_date) VALUES (?,?,?,?,?,?,?);';
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			//if user put code
			exit();
		}
		mysqli_stmt_bind_param($stmt, "isiiiii", $libID, $action, $toLibID, $commentID, $postID, $bookID, $date);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		$sql =
			'INSERT INTO unseen_notifications(lib_id) VALUES (?);';
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
			//if user put code
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $toLibID);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
}

function getPostedLibID($conn, $postID)
{
	return fetch($conn, 'SELECT lib_id FROM posts WHERE post_id =' . $postID . ';')[0]['lib_id'];
}
function ownPost($conn, $postID)
{
	if (own($conn, getPostedLibID($conn, $postID))) {
		return true;
	}
	return false;
}
function getReadingList($conn)
{
	return fetch($conn, 'SELECT lib_id, lib_title, lib_pic,main FROM libraries WHERE lib_id IN (SELECT reading FROM readers WHERE reader = ' . $_SESSION['main_lib_id'] . ') ORDER BY lib_title;');
}
function main($conn, $libID)
{
	$sql = 'SELECT lib_id FROM libraries WHERE lib_id=' . $libID . ' AND main = 1;';
	return fetch($conn, $sql);
}
function findLib($conn, $keywords)
{
	/*
	@* @param array keywords has the search keywords
	*/
	$sql = [];
	foreach ($keywords as $keyword) {
		array_push($sql, ' (lib_title LIKE \'% ' . $keyword . '%\' OR lib_title LIKE \'' . $keyword . '%\') ');
	} //the space after the first like is crucial
	$sql = join('OR', $sql);
	if ($data = fetch($conn, 'SELECT * FROM libraries WHERE ' . $sql . 'ORDER BY case when lib_title LIKE \'' . $keywords[0] . '%\' then 1 else 2 end , lib_title;')) {
		return $data;
	}
	return false;
}

function getListsSimilarToList($conn, $listID)
{
	/*
this function will return the follwoing colums
_order		is category rank times readers count
list_id	
list_title
lib_id
lib_pic	
lib_title
*/
	$listTitle = fetch($conn, 'SELECT title FROM book_lists WHERE list_id=' . $listID . ';')[0]['title'];
	$sql = 'SELECT
c.readers_count*a.rank AS _order,
a.list_id,
b.title AS list_title,
c.lib_id,
c.lib_pic,
c.lib_title
FROM
(
SELECT
	COUNT(list_id) AS rank,
	list_id
FROM
	(
	SELECT
		*
	FROM
		list_categories
	WHERE
		category IN(
		SELECT
			category
		FROM
			list_categories
		WHERE
			list_id = ' . $listID . ' 
	) AND list_id != ' . $listID . ' 
) AS a
GROUP BY
list_id
) AS a
INNER JOIN book_lists AS b
ON
a.list_id = b.list_id
INNER JOIN (SELECT b.lib_id, IF(b.readers_count=0,1,b.readers_count) "readers_count",b.lib_pic,b.lib_title,a.list_id FROM book_lists AS a RIGHT JOIN libraries as b 
ON 
a.lib_id
=
b.lib_id) AS c ON c.list_id=a.list_id
ORDER BY
_order DESC,list_id DESC';
	$data = fetch($conn, $sql);

	$listIDs = array();
	if (!$data) {
		return false;
	}
	foreach ($data as $row) {
		array_push($listIDs, $row['list_id']);
	}
	$sqlComplement = '(' . join(',', $listIDs) . ')';
	$sql = 'SELECT 1 AS _order,a.list_id,a.title,a.lib_id AS list_title,b.lib_id,b.lib_pic,b.lib_title FROM book_lists AS a  INNER JOIN libraries AS b ON b.lib_id=a.lib_id WHERE a.title LIKE \'' . $listTitle . '%\' AND list_id NOT IN ' . $sqlComplement . ' AND a.list_id != ' . $listID . ';';
	if ($data2 = fetch($conn, $sql)) {
		$data = array_merge($data, $data2);
	}
	return $data;
}
function getAvailableCategories($conn, $CategoryKey, $chosenCategories)
{
	$sqlComplement = '';
	if ($chosenCategories) {
		$chosenCategories = implode("','", explode(',', $chosenCategories));
		$chosenCategories = '\'' . $chosenCategories . '\'';
		$sqlComplement = ' AND category NOT IN (' . $chosenCategories . ')';
	}
	$sql = 'SELECT * FROM available_categories WHERE category LIKE \'' . $CategoryKey . '%\'' . $sqlComplement . ' LIMIT 10;';
	return fetch($conn, $sql);
}
function getPost($conn, $postID)
{
	$sql = 'SELECT * FROM posts WHERE post_id= ' . $postID . ' ;';
	return fetch($conn, $sql)[0];
}
function isPostLiked($conn, $postID, $libID)
{
	if (fetch($conn, 'SELECT * FROM post_likes WHERE post_id =' . $postID . ' AND lib_id=' . $libID . ' ;')) {
		return true;
	}
	return false;
}
function isCommentInPost($conn, $postID, $commentID)
{
	if (fetch($conn, 'SELECT * FROM comments WHERE post_id = ' . $postID . ' AND comment_id =' . $commentID . ';')) return true;
	return false;
}
function getComment($conn, $commentID)
{
	return fetch($conn, 'SELECT * FROM comments WHERE comment_id =' . $commentID . ';')[0];
}
