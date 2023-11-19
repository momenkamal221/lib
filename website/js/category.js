let availableCategories = new Array();
const categoriesC = document.getElementById("categoriesC");

const ul = document.getElementById("enteredCategories"),
	input = document.getElementById("enterCategory"),
	tagNumb = document.getElementById("NOCategories");
let maxTags = 10,
	tags = new Array();
tags = [];
countTags();
createTag();

function countTags() {
	input.focus();
	tagNumb.innerText = maxTags - tags.length;
}

function createTag() {
	ul.querySelectorAll("li").forEach((li) => li.remove());
	tags
		.slice()
		.reverse()
		.forEach((tag) => {
			let liTag = `<li>${tag} <i class="uit uit-multiply" onclick="remove(this, '${tag}')"></i></li>`;
			ul.insertAdjacentHTML("afterbegin", liTag);
		});
	countTags();
}

function remove(element, tag) {
	let index = tags.indexOf(tag);
	tags = [...tags.slice(0, index), ...tags.slice(index + 1)];
	element.parentElement.remove();
	countTags();
}
function getCategories() {
	post(
		"processes.php",
		{
			process: 20,
			category: input.value,
			chosenCategories:tags
		},
		(res) => {

			res = JSON.parse(res);
			availableCategories = res;
			displayCategories();
		}
	);
}
function addCategory(Category) {
	let tag = Category;
	if(tag)
	if (tag.length > 1 && !tags.includes(tag)) {
		if (tags.length < 10) {
			tags.push(tag);
			createTag();
		}
	}
	input.value = "";
}
function inputEntered(e) {
	if(e.key=='Enter'){
		addCategory(availableCategories[0]);
	}
	getCategories();
}
function categoryClicked(e){
	addCategory(e.innerText);
	getCategories();
}
function displayCategories() {
	let displayedCategories = document.getElementsByClassName("category");
	let categoriesToRemove = [];

	for (let dispayedCategory of displayedCategories) {
		categoriesToRemove.push(dispayedCategory);
	}
	for (let categoryToRemove of categoriesToRemove) {
		categoryToRemove.remove();
	}
	if (availableCategories)
		for (let category of availableCategories) {
			categoriesC.insertAdjacentHTML(
				"beforeend",
				`<div class="category" onClick="categoryClicked(this)">${category}</div>`
			);
		}
}

input.addEventListener("keyup", inputEntered);

const removeBtn = document.querySelector(".details button");
removeBtn.addEventListener("click", () => {
	tags.length = 0;
	ul.querySelectorAll("li").forEach((li) => li.remove());
	countTags();
});
