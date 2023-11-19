//login vars
const accountSlab = document.getElementById("accountSlab");
const accountField = accountSlab.getElementsByTagName("input")[0];
const passwordSlab = document.getElementById("passwordSlab");
const passwordField = passwordSlab.getElementsByTagName("input")[0];
const noAccMsg = document.getElementById("noAcc");
//signup vars
const fnameSlab = document.getElementById("fnameSlab");
const fnameField = fnameSlab.getElementsByTagName("input")[0];
const lnameSlab = document.getElementById("lnameSlab");
const lnameField = lnameSlab.getElementsByTagName("input")[0];
const emailSlab = document.getElementById("emailSlab");
const emailField = emailSlab.getElementsByTagName("input")[0];
const pssSlab = document.getElementById("pssSlab");
const pssField = pssSlab.getElementsByTagName("input")[0];
const calSlab = document.getElementById("calSlab");
const day = document.getElementById("day");
const month = document.getElementById("month");
const year = document.getElementById("year");
const submissionMsg = document.getElementById("submissionMsg");
let errors;

let showSignUpC = false;

function post(url, data, success) {
	let init = {};
	init.method = "POST";
	init.body = Object.entries(data)
		.map(([k, v]) => {
			return k + "=" + v;
		})
		.join("&");
	init.headers = { "Content-Type": "application/x-www-form-urlencoded" };

	async function postPromise() {
		try {
			let res = await fetch(url, init);
			return await res.text();
		} catch (error) {
			console.log(error);
		}
	}
	if (typeof success === "function") {
		postPromise().then((res) => {
			success(res);
		});
	}
}
function sendErr(msgID, slab, field) {
	switch (msgID) {
		case 0:
			slab.classList.add("signup_err");
			slab.getElementsByClassName(
				"signup-c__err-msg"
			)[0].innerText = `${field.placeholder} cannot be empty`;
			break;
		case 1:
			slab.classList.add("signup_err");
			slab.getElementsByClassName(
				"signup-c__err-msg"
			)[0].innerText = `Looks like this is not an email`;
			field.placeholder = `email@example/com`;
			field.value = "";
			break;
		case 2:
			slab.classList.add("signup_err");
			slab.getElementsByClassName(
				"signup-c__err-msg"
			)[0].innerText = `This email is already existed`;
			break;
		case 3:
			submissionMsg.innerText = "Wrong date";
			break;
		case 4:
			slab.classList.add("signup_err");
			slab.getElementsByClassName(
				"signup-c__err-msg"
			)[0].innerText = `The password can't be less than 8 characters`;
			break;
		case 5:
			noAccMsg.style.display = "block";
			break;
	}
}

function signupValidationCheck() {
	//show
	errors = Array(4);
	if (showSignUpC === false) {
		document.getElementsByClassName("signup-c")[0].classList.add("show");
		showSignUpC = true;
		return;
	}

	fnameSlab.classList.remove("signup_err");
	if (emptyCheck(fnameField.value)) {
		sendErr(0, fnameSlab, fnameField);
		errors[0] = true;
	}

	lnameSlab.classList.remove("signup_err");
	if (emptyCheck(lnameField.value)) {
		sendErr(0, lnameSlab, lnameField);
		errors[1] = true;
	}

	emailSlab.classList.remove("signup_err");
	var reg =
		/(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
	if (!emailField.value.match(reg)) {
		sendErr(1, emailSlab, emailField);
		errors[2] = true;
	}

	pssSlab.classList.remove("signup_err");
	if (emptyCheck(pssField.value)) {
		sendErr(0, pssSlab, pssField);
		errors[3] = true;
	}

	for (error of errors) {
		if (error) {
			return false;
		}
	}
	return true;
}

function emptyCheck(Field) {
	if (Field) return false;
	return true;
}

function submitSingUp() {
	if (signupValidationCheck()) {
		post(
			`signup.php`,
			{
				fname: fnameField.value,
				lname: lnameField.value,
				email: emailField.value,
				pwd: pssField.value,
				day: day.value,
				month: month.value,
				year: year.value,
			},
			(res) => {
				res = JSON.parse(res);
				const errs = res.errs;
				if (errs.includes(2)) {
					sendErr(2, emailSlab, emailField);
				}
				if (errs.includes(3)) {
					sendErr(3);
				}
				if (errs.includes(4)) {
					sendErr(4, pssSlab, emailField);
				}
				if (errs.includes(-1)) {
					window.open("./", "_self");
				} else {
					submissionMsg.innerHTML = "something went wrong";
				}
			}
		);
	}
}
function loginValidationCheck() {
	errors = Array(2);
	if (emptyCheck(accountField.value)) {
		sendErr(0, accountSlab, accountField);
		errors[0] = true;
	}
	if (emptyCheck(passwordField.value)) {
		sendErr(0, passwordSlab, passwordField);
		errors[1] = true;
	}
	for (error of errors) {
		if (error) {
			return false;
		}
	}
	return true;
}
function login() {
	if (loginValidationCheck()) {
		post(
			`login.php`,
			{
				email: accountField.value,
				pwd: passwordField.value,
			},
			(res) => {
				res = JSON.parse(res);
				const errs = res.errs;
				noAccMsg.style.display = "none";

				accountSlab.classList.remove("signup_err");
				passwordSlab.classList.remove("signup_err");
				if (errs.includes(5)) {
					sendErr(5);
				}
				if (errs.includes(-1)) {
					window.open("./", "_self");
				}
			}
		);
	}
}
function hideForm() {
	if (showSignUpC === true) {
		document.getElementsByClassName("signup-c")[0].classList.remove("show");
		showSignUpC = false;
	}
}
// code
document.getElementById("signupB").addEventListener("click", submitSingUp);
document.getElementById("signinB").addEventListener("click", login);
document.getElementById("hide-btn").addEventListener("click", hideForm);

document.body.addEventListener("keyup", function (event) {
	if (event.code === "Enter") {
		if (Array(accountField, passwordField).includes(document.activeElement)) {
			login();
		} else if (
			Array(fnameField, lnameField, pssField, emailField).includes(
				document.activeElement
			)
		) {
			submitSingUp();
		}
	}
});
